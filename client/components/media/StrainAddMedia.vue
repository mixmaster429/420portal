<template>
    <div @click="addMedia = true" color="primary" text-color="#000" type="flat">
        <img class="img-fluid h-50" src="/imgs/strain_add_media.png" alt="Add Strain" />

        <vs-popup class="strains__popup media__add" type="border" title :active.sync="addMedia">
            <add-form :from="from" mode="add" :strain="strain_data.strain"></add-form>
        </vs-popup>
    </div>
</template>

<script>
import AddForm from "./AddForm";
export default {
    name: "StrainAddMedia",    
    props: ["strain_data", "from"],
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
        console.log(this.strain_data)
    },
    methods: {
        showAddModal() {
            this.addMedia = true;
        }
    }
};
</script>
