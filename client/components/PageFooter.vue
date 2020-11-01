<template>
    <div id="page_footer" class="container-fluid">
        <div class="container footer_content">
            <div class="title_footer">
                <span class="fontsize-14"><strong>420Portal.com</strong></span>
            </div>

            <div class="widget-footer text-center">
                <h2>Follow <span>Us on</span></h2>
                <p class="social_icons">
                    <a href="https://instagram.com/420portal" target="_blank"><fa :icon="['fab', 'instagram']" fixed-width /></a>
                    <a href="https://youtube.com/user/420portal" target="_blank"><fa :icon="['fab', 'youtube-square']" fixed-width /></a>
                    <a href="https://twitter.com/420portal" target="_blank"><fa :icon="['fab', 'twitter-square']" fixed-width /></a>
                    <a href="https://facebook.com/420portal" target="_blank"><fa :icon="['fab', 'facebook-square']" fixed-width /></a>
                </p>
                <div class="text-center">
                    <a href="/portals/add" class="link-add add_portal">
                        <img src="~assets/imgs/add_portal.png" alt="">
                        <p>Add</p>
                        <p>Dispensary/Delivery</p>
                    </a>
                    <a href="/brand/add" class="link-add add_brand">
                        <img src="~assets/imgs/add_brand.png" alt="">
                        <p>Add</p>
                        <p>Brand</p>
                    </a>
                </div>

                <div class="footer-divider"></div>

                <div class="footer_link">
                    <div class="footer-navigation">
                        <ul>
                            <li><a href="/">Weedgram </a>|</li>
                            <li><a href="/medical-recreational-marijuana-dispensary-delivery">Dispensary/Delivery </a>|</li>
                            <li><a href="/marijuana-strains">Strains</a>|</li>
                            <li><a href="/marijuana-coupons">Coupons</a>|</li>
                            <li><a href="/marijuana-news">News</a>|</li>
                            <li class="last"><a href="/marijuana-forums">Forums</a>|</li>
                            <li><a href="/contact-us">Contact Us</a></li>
                        </ul>
                    </div>

                    <div class="footer-copyright">
                        <p>All Rights Reserved. Copyright www.420portal.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        name: "pagefooter",
        mounted() {

        }
    }
</script>

<style scoped lang="scss">
    .link-add {
        display: inline-block;
        width: 130px;
        &.add_portal {
            text-align: center;
        }
        &.add_brand {
            img {
                width: 40px;
            }
            p {
                margin: 0;
                color: white;
                font-size: 14px;
                line-height: 14px;
            }
        }
    }
</style>
