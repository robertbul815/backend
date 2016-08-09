
                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                    
                    	<div class="col-lg-4 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>
                                        <?php echo $count_registered_users; ?>
                                    </h3>
                                    <p>
                                        Registered Users
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="<?php echo base_url(); ?>users/edit" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        
                        <div class="col-lg-4 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-light-blue">
                                <div class="inner">
                                    <h3>
                                        <?php echo $count_posts; ?>
                                    </h3>
                                    <p>
                                        Posted Workouts
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <a href="<?php echo base_url(); ?>posts/edit" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        
                    </div><!-- /.row -->

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
		</div>