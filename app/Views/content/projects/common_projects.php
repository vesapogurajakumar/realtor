<section class="property" id="property">
        <div class="container">

          <p class="section-subtitle">Properties</p>

          <h2 class="h2 section-title">Featured Listings</h2>

          <ul class="property-list has-scrollbar">
            <?php if(isset($latest_feed) && !empty($latest_feed)) {
              foreach ($latest_feed as $value) { ?>
            <li>
              <div class="property-card">

                <figure class="card-banner">

                  <a href="#">
                    <img src="<?= isset($value['src']) ? $value['src'] : '' ?>" alt="New Apartment Nice View" class="w-100">
                  </a>

                  <div class="card-badge green">For Rent</div>

                  <div class="banner-actions">

                    <button class="banner-actions-btn">
                      <ion-icon name="location"></ion-icon>

                      <address><?= isset($value['address']) ? $value['address'] : '' ?></address>
                    </button>

                    <button class="banner-actions-btn">
                      <ion-icon name="camera"></ion-icon>

                      <span>4</span>
                    </button>

                    <button class="banner-actions-btn">
                      <ion-icon name="film"></ion-icon>

                      <span>2</span>
                    </button>

                  </div>

                </figure>

                <div class="card-content">

                  <div class="card-price">
                    <strong><?= isset($value['price']) ? $value['price'] :'$100' ?></strong>/Month
                  </div>

                  <h3 class="h3 card-title">
                    <a href="#"> <?= isset($value['discription']) ? $value['discription'] :'New Apartment Nice View' ?> </a>
                  </h3>

                  <p class="card-text">
                    <?= isset($value['discription2']) ? $value['discription2'] :'New Apartment Nice View' ?>
                  </p>

                  <ul class="card-list">

                    <li class="card-item">
                      <strong> <?= isset($value['features']['key1value']) ? $value['features']['key1value'] : '' ?> </strong>

                      <ion-icon name="bed-outline"></ion-icon>

                      <span><?= isset($value['features']['key1']) ? $value['features']['key1'] : '' ?></span>
                    </li>

                    <li class="card-item">
                      <strong><?= isset($value['features']['key2value']) ? $value['features']['key2value'] : '' ?></strong>

                      <ion-icon name="man-outline"></ion-icon>

                      <span><?= isset($value['features']['key2']) ? $value['features']['key2'] : '' ?></span>
                    </li>

                    <li class="card-item">
                      <strong><?= isset($value['features']['key3value']) ? $value['features']['key3value'] : '' ?></strong>

                      <ion-icon name="square-outline"></ion-icon>

                      <span><?= isset($value['features']['key3']) ? $value['features']['key3'] : '' ?></span>
                    </li>

                  </ul>

                </div>

                <div class="card-footer d-none">

                  <div class="card-author">

                    <figure class="author-avatar">
                      <img src="./assets/images/author.jpg" alt="William Seklo" class="w-100">
                    </figure>

                    <div>
                      <p class="author-name">
                        <a href="#">William Seklo</a>
                      </p>

                      <p class="author-title">Estate Agents</p>
                    </div>

                  </div>

                  <div class="card-footer-actions">

                    <button class="card-footer-actions-btn">
                      <ion-icon name="resize-outline"></ion-icon>
                    </button>

                    <button class="card-footer-actions-btn">
                      <ion-icon name="heart-outline"></ion-icon>
                    </button>

                    <button class="card-footer-actions-btn">
                      <ion-icon name="add-circle-outline"></ion-icon>
                    </button>

                  </div>

                </div>

              </div>
            </li>
            <?php }
                  }?>
          </ul>

        </div>
    </section>



