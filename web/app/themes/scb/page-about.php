<?php
/**
 * Template Name: About
 */

$num_design_professionals = \Firebelly\SiteOptions\get_option('num_design_professionals');
$num_completed_projects = \Firebelly\SiteOptions\get_option('num_completed_projects');
$num_offices = \Firebelly\PostTypes\Office\get_num_offices();
$completed_projects_map_image = \Firebelly\SiteOptions\get_option('completed_projects_map_image');
$slideshow_images = get_post_meta($post->ID, '_cmb2_slideshow_images', true);
$secondary_content = get_post_meta($post->ID, '_cmb2_secondary_content', true);
?>

<div class="top-section page-content grid wrap">

  <div class="page-intro grid-item one-half -left">
    <div class="-top">
      <?= apply_filters('the_content', $post->post_content) ?>

      <?php if (!empty($slideshow_images)): ?>
        <?= \Firebelly\Utils\image_slideshow($slideshow_images); ?>
      <?php endif; ?>

      <?= !empty($secondary_content) ? apply_filters('the_content', $secondary_content) : '' ?>
    </div>
  </div>

  <div class="grid-item -right">
    <div class="stats">
      <div class="stat long-stat">
        <div class="wrap">
          <p class="stat-number"><?= $num_offices ?></p>
          <p class="stat-label">Offices</p>
          <p class="stat-link"><a href="/office/chicago" class="show-post-modal">Chicago</a> / <a href="/office/san-francisco" class="show-post-modal">San Francisco</a></p>
        </div>
      </div>
      <div class="stat long-stat">
        <div class="wrap">
          <p class="stat-number"><?= $num_design_professionals ?></p>
          <p class="stat-label">Design Professionals</p>
          <p class="stat-link"><a href="/people/">Meet our people</a></p>
        </div>
      </div>
      <div class="stat long-stat">
        <div class="wrap">
          <p class="stat-number"><?= $num_completed_projects ?></p>
          <p class="stat-label">Completed Projects</p>
          <?php if ($completed_projects_map_image): ?>
            <p class="stat-link"><a href="<?= $completed_projects_map_image ?>" class="show-image-modal">View on Map</a></p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <section class="design-principles">
      <h2>SCB Design Principles</h2>
      <div class="design-principle"><div class="-left"><h3>Interpret</h3> <span class="dash">—</span></div><p>Every project begins with a conversation. We work closely with our clients to understand their goals and interpret their vision.</p></div>
      <div class="design-principle"><div class="-left"><h3>Envision</h3> <span class="dash">—</span></div><p>Our designs combine artistic expression with technical rigor, integrating sustainability and vision to create solutions that uniquely suit our clients’ needs.</p></div>
      <div class="design-principle"><div class="-left"><h3>Execute</h3> <span class="dash">—</span></div><p>A project isn’t successful until it’s delivered successfully. Our team has developed a strong reputation for seamless project management that ensures projects are delivered on time and on budget.</p></div>
      <div class="design-principle"><div class="-left"><h3>Inspire</h3> <span class="dash">—</span></div><p>Real design solutions inspire. SCB’s work is tangible, and our projects strive to make positive contributions to people’s lives and the urban fabric.</p></div>
    </section>
  </div>

</div>

<section class="firm-history page-content wrap" id="firm-history">
  <h2>Firm History</h2>

  <div class="section -top grid">

    <div class="grid-item -left">
      <h3>Since its inception in 1931, SCB has embraced a future-oriented design ethos that places inventiveness and innovation at the firm’s core.</h3>
      <p>As the economy improved after the Great Depression, Lou and Irving Solomon saw an opportunity for growth by expanding the firm’s work into the residential market. Lou began acquiring properties along Chicago’s Lake Shore Drive and designing high-rise rental apartment communities. Meanwhile, Irving was the contractor and their sister Sylvia managed the projects. Through his foresight, Lou created a unique end-to-end business model for successful buildings that still stand today. Although now solely a design firm, the legacy of a design-build-operate approach laid the foundation for a design culture that understands and addresses all aspects a building’s needs for marketability, constructability, and operations.</p>
    </div>

    <div class="image-with-stat grid-item -right">
      <img src="<?= Roots\Sage\Assets\asset_path('images/about-Cordwell-&-Buenz.jpg') ?>" alt="Cordwell &amp; Buenz">

      <div class="stat">
        <div class="wrap">
          <p class="stat-number">'56</p>
          <p class="stat-label">Lou Solomon and<br> John Cordwell meet</p>
        </div>
      </div>
    </div>

  </div>

  <div class="section -second grid">

    <div class="image-with-stat -left">
      <img src="<?= Roots\Sage\Assets\asset_path('images/about-1964_Carl_Sandburg_Village.jpg') ?>" alt="1964 - Carl Sandburg Village">

      <div class="stat">
        <div class="wrap">
          <p class="stat-number">'64</p>
          <p class="stat-label">Won citywide competition to<br> design Carl Sandburg Village</p>
        </div>
      </div>
    </div>

  </div>

  <div class="section -third grid">

    <div class="grid-item -right">
      <p>In 1956, Lou Solomon hired John Cordwell. An Englishman who fought as a bombardier in World War II, and whose experience in a Nazi POW camp was part of the inspiration for the movie The Great Escape, Cordwell had recently resigned as Chicago’s Director of Planning. During his tenure, he had been instrumental in convincing Mayor Kennelly to plan for mass transit trains down the center of the city’s new Eisenhower and Kennedy expressways. </p>

      <p>In 1957, Solomon and Cordwell won a citywide competition to design Carl Sandburg Village. The final project comprised over 2500 units of market rate rentals with a mix of high-, mid-, and low-rise residential building types. With amenities like townhomes surrounding under- and above-ground structured parking and landscaped plazas, the project became a national model for urban redevelopment. A year later, when Solomon was on a business trip, Cordwell had new business cards made: L.R. Solomon and J.D. Cordwell & Associates Architects. The bold tactic solidified their partnership.</p>
    </div>

  </div>

  <div class="section -fourth grid">

    <div class="grid-item -left">
      <div class="image-with-stat">
        <img src="<?= Roots\Sage\Assets\asset_path('images/about-1970_EdgewaterPlaza_bw.jpg') ?>" alt="Edgewater Plaza">

        <div class="stat">
          <div class="wrap">
            <p class="stat-number">'70</p>
            <p class="stat-label">Edgewater Plaza</p>
          </div>
        </div>
      </div>

      <div class="section-text">
        <p>In 1963, John Cordwell hired the firm’s third partner, John Buenz. An apprentice under Eero Saarinen and Harry Weese, Buenz started as a design architect and was surprised when his name was added to the letterhead just five years later. Together, the three men set the foundation for what would become a powerful high-rise residential practice, one that still thrives today.</p>

        <p>In the 70s, Solomon Cordwell Buenz expanded once more to begin working in the hospitality, office, and retail markets. The firm created inventive spaces that shifted market paradigms, as with the Crate & Barrel Flagship store on Chicago’s Magnificent Mile, whose design by Buenz redefined the character of this renowned retail thoroughfare.</p>
      </div>
    </div>

    <div class="grid-item -right">
      <div class="image-with-stat">
        <img src="<?= Roots\Sage\Assets\asset_path('images/about-1980_1418N.LSDApartmentsBW.jpg') ?>" alt="Lakeshore Drive Apartments">

        <div class="stat">
          <div class="wrap">
            <p class="stat-number">'80</p>
            <p class="stat-label">Lakeshore Drive Apartments</p>
          </div>
        </div>
      </div>

    </div>

  </div>

  <div class="section -fifth grid">

    <div class="grid-item -left">
      <div class="image-with-stat">
        <img src="<?= Roots\Sage\Assets\asset_path('images/about-Crate&Barrel.jpg') ?>" alt="Crate &amp; Barrel Flagship store on Chicago’s Magnificent Mile">

        <div class="stat">
          <div class="wrap">
            <p class="stat-number">'90</p>
            <p class="stat-label">Crate &amp; Barrel Flagship store<br> on Chicago’s Magnificent Mile</p>
          </div>
        </div>
      </div>

      <div class="section-text">
        <p>SCB continues to build on this legacy, boasting a staff of over 240 design professionals with more than 650 completed projects across a wide range of building types. In 2007, the firm opened an office in San Francisco, and since then has made extensive contributions to the city’s skyline and neighborhoods. The firm’s first project in San Francisco, One Rincon Hill, follows in the SCB tradition of using creative design tactics to deliver unique solutions. Designed as a catalyst for urban renewal, the slender tower’s 60-story height was made possible by integrating unique architectural and structural solutions for building in an active seismic zone.</p>

        <p>Today we honor the spirit of our founders in projects all over the country that push the boundaries of design to deliver innovative, effective solutions ranging from architecture to planning to interior design. At 83, John Buenz still stops in to visit from time to time. When he does, he urges us to think outside the box and keep looking to the future by reminding us of the advice John Cordwell once gave him: “You’ve got to think laterally!” </p>

      </div>
    </div>

    <div class="grid-item -right">
      <div class="image-with-stat">
        <img src="<?= Roots\Sage\Assets\asset_path('images/about-2001-sunwall.jpg') ?>" alt="Sun Wall">

        <div class="stat">
          <div class="wrap">
            <p class="stat-number">'01</p>
            <p class="stat-label">US Department of Energy<br> Sun Wall</p>
          </div>
        </div>
      </div>

      <img src="<?= Roots\Sage\Assets\asset_path('images/about-Sunset-stripes-H-Kam-2008.jpg') ?>" alt="Present-day SCB">
    </div>

  </div>

</section>