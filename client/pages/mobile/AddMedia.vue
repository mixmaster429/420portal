<template>
    <div>
        <div
            class="sticky-top strains__sticky"
            style="margin-right: -15px; margin-left: -15px; background: #000;width:unset;"
        >
        <ul class="nav strains__nav">
            <p style="margin: auto 5px; padding: 8px; font-size: 22px;color:#EFA720;">
                <fa @click="$router.go(-1)" icon="times" class="mr-2" fixed-width></fa> Media
            </p>
        </ul>
        </div>
        <div class="strains__popup media__add" style="margin-top: 2rem;margin-bottom: 5rem;">
            <add-form mode="add" :from="from"></add-form>
        </div>
    </div>
</template>

<script>
import AddForm from "~/components/media/AddForm";
export default {
    name: "AddMedia",
    props: ['from'],
    components: {
        AddForm,
    },
    data() {
        return {};
    },
    mounted() {
        // this.getData();
    },
    methods: {}
};
</script>