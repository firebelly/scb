def wf_obj_exists(type, check, value)
  objs = @wf_server.call("list_#{type}", @wf_session);
  objs.each do |obj|
    if obj[check] == value
      return true 
    end
  end
  return false
end

def wf_get_ip_address
  puts "Getting IP addresses..."
  @wf_ip_address = ''
  ip_info = @wf_server.call('list_ips', @wf_session);
  ip_info.each do |info|
    if info['is_main']
      @wf_ip_address = info['ip']
    end
  end
  puts "Main IP: #{@wf_ip_address}"
end

def wf_api_connect
  @wf_server = XMLRPC::Client.new2('https://api.webfaction.com/')
  @wf_session, @wf_account = @wf_server.call('login', ENV['WF_USER'], ENV['WF_PASSWORD'])
  puts "Connected to WebFaction API: #{@wf_session}, #{@wf_account}"
end

namespace :deploy do
  task :wf_delete do
    require 'xmlrpc/client'
    require 'dotenv'
    Dotenv.load(".env.#{fetch(:stage)}")
    wf_api_connect
    wf_get_ip_address

    begin
      if wf_obj_exists('db_users', 'username', ENV['DB_USER'])
        puts @wf_server.call('delete_db_user', @wf_session, ENV['DB_USER'], 'mysql');
      end
      if wf_obj_exists('dbs', 'name', ENV['DB_NAME'])
        puts @wf_server.call('delete_db', @wf_session, ENV['DB_NAME'], 'mysql');
      end
      if wf_obj_exists('apps', 'name', fetch(:application))
        puts @wf_server.call('delete_app', @wf_session, "#{fetch(:application)}");
      end
      if wf_obj_exists('apps', 'name', "#{fetch(:application)}_web")
        puts @wf_server.call('delete_app', @wf_session, "#{fetch(:application)}_web");
      end
      puts @wf_server.call('delete_website', @wf_session, "#{fetch(:application)}", @wf_ip_address, false);
    rescue Exception => e
      puts "Could not delete apps & dbs: #{e.message}"
    end
  end

  task :wf_setup do
    require 'xmlrpc/client'
    require 'dotenv'
    Dotenv.load(".env.#{fetch(:stage)}")

    wf_api_connect
    wf_get_ip_address

    # delete default app and website
    if wf_obj_exists('websites', 'name', fetch(:login))
      begin
        puts "Deleting default htdocs app and '#{fetch(:login)}' website..."
        ret = @wf_server.call('delete_website', @wf_session, fetch(:login), @wf_ip_address, false);
        puts ret
        ret = @wf_server.call('delete_app', @wf_session, 'htdocs');
        puts ret
      rescue Exception => e
        puts "Unable to delete default app and website: #{e.message}"
      end
    end

    # create db_user
    if !wf_obj_exists('db_users', 'username', ENV['DB_USER'])
      begin
        puts "Creating db_user #{ENV['DB_USER']}..."
        ret = @wf_server.call('create_db_user', @wf_session, ENV['DB_USER'], ENV['DB_PASSWORD'], 'mysql');
        puts ret
      rescue Exception => e
        puts "Unable to create db_user #{ENV['DB_USER']}: #{e.message}"
      end
    end

    # create db & assign db_user
    if !wf_obj_exists('dbs', 'name', ENV['DB_NAME'])
      begin
        puts "Creating db #{ENV['DB_NAME']}..."
        ret = @wf_server.call('create_db', @wf_session, ENV['DB_NAME'], 'mysql', '', ENV['DB_USER']);
        puts ret
      rescue Exception => e
        puts "Unable to create db #{ENV['DB_NAME']}: #{e.message}"
      end
    end

    # create static PHP app
    if !wf_obj_exists('apps', 'name', fetch(:application))
      begin
        puts "Creating static app #{fetch(:application)}..."
        ret = @wf_server.call('create_app', @wf_session, fetch(:application), "static_#{fetch(:php)}");
        puts ret
      rescue Exception => e
        puts "Unable to create app #{fetch(:application)}: #{e.message}"
      end
    end

    # create dirs for app
    puts "Creating /shared/web/app/uploads directories..."
    on roles :web do
      execute :mkdir, "-p #{release_path.join('../shared/web/app/uploads')}"
    end

    # install Composer for deploys
    puts "Installing Composer..."
    on roles :web do
      if test("[ -f /home/#{fetch(:login)}/bin/composer.phar ]")
        puts "Composer already installed."
      else
        within "/home/#{fetch(:login)}/bin" do
          execute :curl, "-sS https://getcomposer.org/installer | #{fetch(:php)}"
          execute :echo, "-e \"\n# Composer\nalias composer=\\\"#{fetch(:php)} \$HOME/bin/composer.phar\\\"\" >> $HOME/.bash_profile"
        end
      end
    end

    # delete default index.html file if it exists
    on roles :web do
      execute :rm, "-f #{release_path.join('../index.html')}"
    end

    # scp .env and .htaccess
    puts "Uploading /shared/.env and /shared/web/.htaccess..."
    on roles :web do
      if test("[ -f #{release_path.join('../shared/.env')} ]")
        puts ".env exists."
      else
        upload! fetch(:local_abs_path).join(".env.#{fetch(:stage)}").to_s, release_path.join('../shared/.env')
      end

      if test("[ -f #{release_path.join('../shared/web/.htaccess')} ]")
        puts ".htaccess exists."
      else
        upload! fetch(:local_abs_path).join('web/.htaccess').to_s, release_path.join('../shared/web/.htaccess')
      end
    end

    # create symbolic PHP app pointing to current dir
    if !wf_obj_exists('apps', 'name', "#{fetch(:application)}_web")
      begin
        puts "Creating symbolic app '#{fetch(:application)}_web' of type 'symlink#{fetch(:php).gsub(/php/,'')}'..."
        ret = @wf_server.call('create_app', @wf_session, "#{fetch(:application)}_web", "symlink#{fetch(:php).gsub(/php/,'').to_s}", false, release_path.join('web').to_s);
        puts ret
      rescue Exception => e
        puts "symlink#{fetch(:php).gsub(/php/,'')}: #{e.message}"
      end
    end

    if !wf_obj_exists('websites', 'name', "#{fetch(:application)}")
      begin
        puts "Creating website #{fetch(:application)}..."
        ret = @wf_server.call('create_website', @wf_session, "#{fetch(:application)}", @wf_ip_address, false, [fetch(:domain)], ["#{fetch(:application)}_web", '/']);
        puts ret
      rescue Exception => e
        puts "Unable to create website #{fetch(:application)}: #{e.message}"
      end
    end
  end
end
