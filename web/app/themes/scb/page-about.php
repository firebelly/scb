<?php
/**
 * Template Name: About
 */

$num_projects = \Firebelly\PostTypes\Project\get_num_projects();
$num_people = \Firebelly\PostTypes\Person\get_num_people();
$num_offices = \Firebelly\PostTypes\Office\get_num_offices();

$chicagoId = url_to_postid('office/chicago');
$sanFranciscoId = url_to_postid('office/san-francisco');

$map_id = \Firebelly\Utils\get_id_by_slug('map');
?>


<div class="top-section page-content grid wrap">

  <div class="page-intro grid-item one-half -left">
    <div class="-top">    
      <?= $post->post_content ?>
    </div>
  </div>

  <div class="grid-item -right">
    <div class="stats">
      <div class="stat long-stat">
        <div class="wrap">
          <p class="stat-number"><?= $num_offices ?></p>
          <p class="stat-label">Offices</p>
          <p class="stat-link"><a href="office/chicago" class="show-post-modal" data-id="<?= $chicagoId ?>" data-modal-type="office">Chicago</a> / <a href="office/san-francisco" class="show-post-modal" data-id="<?= $sanFranciscoId ?>" data-modal-type="office">San Francisco</a></p>
        </div>
      </div>
      <div class="stat long-stat">
        <div class="wrap">
          <p class="stat-number"><?= $num_people ?></p>
          <p class="stat-label">Design Professionals</p>
          <p class="stat-link"><a href="people">Meet our people</a></p>
        </div>
      </div>
      <div class="stat long-stat">
        <div class="wrap">
          <p class="stat-number"><?= $num_projects ?></p>
          <p class="stat-label">Active Projects</p>
          <p class="stat-link"><a href="map" class="show-map" data-id="<?= $map_id ?>">View on map</a></p>
        </div>
      </div>
    </div>

    <section class="design-principles">
      <h2>Principles</h2>
      <div><h3>Interpret</h3> <span class="dash">—</span> <p>Every project begins with a conversation. We work closely with our clients to understand their goals and interpret their vision.</p></div>
      <div><h3>Envision</h3> <span class="dash">—</span> <p>Our designs combine artistic expression with technical rigor, integrating sustainability and vision to create solutions that uniquely suit our clients’ needs.</p></div>
      <div><h3>Execute</h3> <span class="dash">—</span> <p>A project isn’t successful until it’s delivered successfully. Our team has developed a strong reputation for seamless project management that ensures projects are delivered on time and on budget.</p></div>
      <div><h3>Inspire</h3> <span class="dash">—</span> <p>Real design solutions inspire. SCB’s work is tangible, and our projects strive to make positive contributions to people’s lives and the urban fabric.</p></div>
    </section>
  </div>

</div>

<section class="firm-history page-content wrap" id="firm-history">
  <h2>Firm History</h2>
  
  <div class="section -top grid">
    
    <div class="grid-item -left">
      <h3>Since its inception in 1931, SCB has embraced a future-oriented design ethos that places inventiveness and innovation at the firm’s core.</h3>
      <p>As the economy improved after the Great Depression, Lou and Irving Solomon saw an opportunity for growth by expanding the firm’s work into new markets. Lou began to acquire properties in Chicago’s Streeterville neighborhood, designing high-rise rental apartment communities along North Lake Shore Drive. Meanwhile, Irving oversaw contracting and construction, and their sister Sylvia managed the properties. Through his foresight, Lou created a unique and robust end-to-end business model for successful buildings that still stand today.</p>
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
          <p class="stat-number">'57</p>
          <p class="stat-label">Winning citywide competition to<br> design Carl Sandburg Village</p>
        </div>
      </div>
    </div>

  </div>
  
  <div class="section -third grid">

    <div class="grid-item -right">
      <p>In 1956, Lou Solomon met John Cordwell. An Englishman who fought as a bombardier in World War II, and whose experience in a Nazi POW camp was inspiration for the movie The Great Escape, Cordwell had recently resigned as Chicago’s Director of Planning. During his tenure, he had been instrumental in convincing Mayor Kennelly to plan for mass transit trains down the center of the city’s new expressways. In 1957, Solomon and Cordwell won a citywide competition to design Carl Sandburg Village. With its mix of residential building types and amenities like underground parking and landscaped plazas, the project became a national model for urban redevelopment. A year later, when Solomon was on a business trip, Cordwell had new business cards made: L.R. Solomon and J.D. Cordwell & Associates Architects. The bold tactic solidified their partnership.</p>
    </div>

  </div>

  <div class="section -fourth grid">
    
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
        <p>In 1963, John Cordwell hired the firm’s third partner, John Buenz. An apprentice under Eero Saarinen and Harry Weese, Buenz started as a design architect and was surprised when his name was added to the letterhead just five years later. Together, the three men built a powerful high-rise residential practice that still thrives today. In the 70s, Solomon Cordwell Buenz expanded once more to begin working in the hospitality, office, and retail markets. The firm created inventive spaces that shifted market paradigms, as with the Crate & Barrel Flagship store on Chicago’s Magnificent Mile, whose design by Buenz redefined the character of this renowned retail thoroughfare.</p> 
        <p>SCB continues to build on this legacy, boasting a staff of 240 design professionals with over 650 projects worldwide in ever-expanding markets. In 2008, the firm opened an office in San Francisco, which has made extensive contributions to the city’s skyline and neighborhoods in just a handful of years. The firm’s first project in the city, One Rincon Hill, follows in the tradition of SCB’s partners by using creative design tactics to deliver a unique solution. Designed as a catalyst for urban renewal, the building’s soaring height was made possible by pioneering structural solutions for building in areas of high seismic activity.</p>
        <p>Today we honor the spirit of our founders in projects all over the country that push the boundaries of design to deliver innovative, effective solutions ranging from architecture to planning to interior design. John Buenz still stops in to visit from time to time. When he does, he urges us to think outside the box and keep looking to the future by reminding us of the advice Lou Solomon once gave him: “You’ve got to think laterally!”</p>
      </div>
    </div>

    <div class="grid-item -right">
      <div class="image-with-stat">
        <img src="<?= Roots\Sage\Assets\asset_path('images/about-OneRinconHill.jpg') ?>" alt="One Rincon Hill">

        <div class="stat">
          <div class="wrap">
            <p class="stat-number">'08</p>
            <p class="stat-label">One Rincon Hill</p>
          </div>
        </div>
      </div>

      <img src="<?= Roots\Sage\Assets\asset_path('images/about-today.jpg') ?>" alt="Present-day SCB">
    </div>

  </div>

</section>