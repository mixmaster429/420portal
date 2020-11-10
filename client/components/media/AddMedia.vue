<template>
    <div class="centerx">
        <div
            v-if="from"
            @click="addMedia=true"
            color="primary"
            text-color="#000"
            type="flat"
        >
            <a @click.prevent href="#" >
                <img src="/imgs/media.png" alt />
                <p>Media</p>
            </a>
        </div>
        <div @click="addMedia=true" color="primary" text-color="#000" type="flat">
            <img class="img-fluid h-50" src="/imgs/add.png" alt="Add Strain" />
            <p>Add Media</p>
        </div>
        <vs-popup v-if="addMedia" class="strains__popup media__add" type="border" title :active.sync="addMedia">
            <add-form :from="from"></add-form>
        </vs-popup>
    </div>
</template>

<script>
import AddForm from "./AddForm";
export default {
    name: "AddMedia",    
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
