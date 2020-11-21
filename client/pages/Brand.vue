<template>
    <brand-show></brand-show>
</template>
<script>
    import BrandShow from "~/components/brand/BrandShow";
    export default {
        head () {
            return { 
                title: 'Marijuana Brands',
            }
        },
        components: {
            BrandShow,
        },        
    }
</script>