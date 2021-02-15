<template>
    <span @click="addMedia = true">
        <img class="img-fluid h-50" src="/imgs/add.png" alt="Add Strain" />
    </span>
    <vs-popup class="strains__popup media__add" type="border" title :active.sync="addMedia">
        <add-form :from="from" mode="add"></add-form>
    </vs-popup>
</template>

<script>
import AddForm from "./AddForm";
export default {
    name: "StrainAddMedia",    
    props: ["from"],
    components: {
        AddForm
    },
    data() {
        return {
            addMedia: false
        };
    },
    mounted() {
        // this.getData();
    },
    methods: {
        showAddModal() {
            this.addMedia = true;
        }
    }
};
</script>
