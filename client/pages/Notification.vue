<template>
    <div class="row justify-content-center">
        <div class="col-xl-6 col-md-8 col-sm-9 px-0 px-md-2">
            <notification></notification>
        </div>
    </div>
</template>

<script>
    import Notification from "~/components/Notification";
    export default {
        middleware: 'auth',
        components: {
            Notification,
        }
    }
</script>