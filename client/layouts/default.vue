<template>
    <div class="layout">
        <top-nav />

        <div class="container-fluid">
            <nuxt />
        </div>

        <bottom-nav />

        <!-- Login Modal -->
        <div class="modal fade" id="loginmodal" style="z-index: 40001">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <img src="~assets/imgs/logo.png" alt="">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <login />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TopNav from '~/components/TopNav'
import BottomNav from '~/components/BottomNav'
import Login from '~/components/Login'

export default {
    components: {
        TopNav, BottomNav, Login
    }
}
</script>
