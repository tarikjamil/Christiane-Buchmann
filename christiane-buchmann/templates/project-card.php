                <div role="listitem" class="w-dyn-item">
                  <a href="<?php echo esc_url(cb_project_url($project['slug'])); ?>" class="project_link w-inline-block">
                    <div class="img_wrapper">
                      <div class="lottie_wrapper">
                        <div data-w-id="aae39708-1fe7-4d84-94cb-4dbcdfc70c93" data-animation-type="lottie" data-src="<?php echo esc_url(cb_asset('documents/lottieflow-play-03-ffffff-easey.json')); ?>" data-loop="1" data-direction="1" data-autoplay="1" data-is-ix2-target="0" data-renderer="svg" data-default-duration="1.125" data-duration="0" class="play_lottie"></div>
                      </div><img src="<?php echo esc_url(cb_upload($project['image'])); ?>"<?php echo cb_size_attrs($project['image']); ?> alt="<?php echo esc_attr(cb_project_alt($project)); ?>" class="project_img" loading="lazy"></div>
                    <div class="project_namecontainer">
                      <h3 id="w-node-4dbcdfc70c96-e9ace7a9" class="h5 difference"><?php cb_e($project['title']); ?></h3><img src="<?php echo esc_url(cb_asset('images/arrow_white.svg')); ?>" id="w-node-4dbcdfc70c97-e9ace7a9" alt="" class="project_arrow"></div>
                  </a>
                </div>
