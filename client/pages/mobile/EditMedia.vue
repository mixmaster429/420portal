<template>
    <div>
        <div
            class="sticky-top strains__sticky"
            style="margin-right: -15px; margin-left: -15px; background: #fff;width:unset;"
        >
        <ul class="nav strains__nav">
            <p style="margin: auto 5px; padding: 8px; font-size: 20px;">
                <fa @click="$router.go(-1)" icon="times" fixed-width class="mr-2"></fa> Edit Media
            </p>
            <div class="ml-auto" style="padding: 8px; font-size: 20px;">
                <fa @click.prevent="$root.$emit('eventing')" icon="check" fixed-wdith class="mr-3 text-420"></fa>
            </div>
        </ul>
        </div>
        <div class="strains__popup media__add" style="margin-top: 2rem;margin-bottom: 5rem;">
            <add-form v-if="mainData" :editData="editData" :mainData="mainData" mode="edit" :from="from"></add-form>
        </div>
    </div>
</template>

<script>
import AddForm from "~/components/media/AddForm";
export default {
    name: "EditMedia",
    components: {
        AddForm,
    },
    props: ["editData", "mainData", "from"],
    data() {
        return {};
    },
    mounted() {
        // this.getData();
    },
    methods: {}
};
</script>