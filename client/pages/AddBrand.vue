<template>
    <div class="row portal_addpage">
        <div class="col-12">
            <h2 class="pa_title">Sign Up for Free!</h2>
            <div class="pa_content">
                <p>Are you a Brand owner?</p>
                <p>Add your Company below and Start Posting.</p>
                <p>And Remember: The More you Post, the More you Grow.</p>
                <p>🌱</p>
            </div>
        </div>
        <div class="add-brand col-xl-3 col-lg-4 col-md-6 col-sm-6 mx-2 mx-md-auto mt-3 border border-white">
            <brand-form :is_edit="false"></brand-form>
        </div>   
        <page-footer></page-footer>
        
        <!-- User Agreement Modal -->
        <div class="modal fade" id="user_agreement_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="text-white">User Agreement</h2>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body"><terms></terms></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import BrandForm from "~/components/brand/BrandForm";
    import PageFooter from "~/components/PageFooter";
    import "../assets/sass/_addportal.scss";
    import Terms from "./auth/terms"
    export default {
        middleware: 'guest',
        components: {
            BrandForm,
            PageFooter,
            Terms,
        }
    }
</script>