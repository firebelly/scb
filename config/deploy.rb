set :application, 'scb'
set :domain, 'scb.firebelly.co'
set :theme, 'scb'
set :login, 'firebelly'
set :repo_url, 'git@github.com:firebelly/scb.git'
set :php, 'php54'

# Hardcodes branch to always be master
# This could be overridden in a stage config file
set :branch, :master

set :deploy_to, -> { "/home/#{fetch(:login)}/webapps/#{fetch(:application)}" }

set :tmp_dir, -> { "/home/#{fetch(:login)}/tmp" }

# Use :debug for more verbose output when troubleshooting
set :log_level, :info

# Apache users with .htaccess files:
# it needs to be added to linked_files so it persists across deploys:
set :linked_files, fetch(:linked_files, []).push('.env', 'web/.htaccess')
set :linked_dirs, fetch(:linked_dirs, []).push('web/app/uploads')

namespace :deploy do
  desc 'Restart application'
  task :restart do
    on roles(:app), in: :sequence, wait: 5 do
      # Your restart mechanism here, for example:
      # execute :service, :nginx, :reload
    end
  end
end

# The above restart task is not run by default
# Uncomment the following line to run it on deploys if needed
# after 'deploy:publishing', 'deploy:restart'

# Set path to Composer on WebFaction
namespace :deploy do
  before :starting, :map_composer_command do
      on roles(:app) do |server|
          SSHKit.config.command_map[:composer] = "#{fetch(:php)} /home/#{fetch(:login)}/bin/composer.phar"
      end
  end
end

# GULP! compile production assets and copy to server, then UNGULP! to dev mode
# borrowing from https://gist.github.com/christhesoul/3c38053971a7b786eff2 & https://gist.github.com/nateroling/22b51c0cfbe210b00698

set :theme_path, Pathname.new('web/app/themes/').join(fetch(:theme))
set :local_app_path, Pathname.new(File.dirname(__FILE__)).join('../')
set :local_abs_path, Pathname.new(File.expand_path File.dirname(__FILE__)).join('../')
set :local_theme_path, fetch(:local_app_path).join(fetch(:theme_path))

require "#{fetch(:local_abs_path)}/config/webfaction.rb"

namespace :deploy do
  task :compile_assets do
    run_locally do
      within fetch(:local_theme_path) do
        execute :gulp, '--production'
      end
    end
  end

  task :ungulp do
    run_locally do
      within fetch(:local_theme_path) do
        execute :gulp, '--development'
      end
    end
  end

  task :copy_assets do
    invoke 'deploy:compile_assets'

    on roles(:web) do
      upload! fetch(:local_theme_path).join('dist').to_s, release_path.join(fetch(:theme_path)), recursive: true
    end

    invoke 'deploy:ungulp'
  end
end

before "deploy:updated", "deploy:copy_assets" unless ENV['NOASS']
