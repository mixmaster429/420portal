<template>
    <div class="row">
        <div class="col-12 px-0">
            <div class="container">
                <notification></notification>
            </div>
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