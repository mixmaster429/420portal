<template>
    <div  color="primary" text-color="#000" type="flat">
      <p class="m-0">
        <span @click="addMedia = true">Upload Your Pictures &amp; Videos of this Strain.</span> 
      </p>
      <p>
        <img class="img-fluid h-50" src="/imgs/strain_add_media.png" alt="Add Strain" @click="addMedia = true" />
      </p>
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
