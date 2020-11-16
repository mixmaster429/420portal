<template>
    <div class="centerx">
        <vs-button @click="popupActivo=true" color="primary" text-color="#efa720" type="flat">
            <img class="img-fluid w-100" src="/imgs/strains/add.png" alt="Add Strain">
        </vs-button>
        <vs-popup class="strains__popup" rype="border" title="Add Strain" :active.sync="popupActivo">
            <form v-on:submit.prevent>
                <div class="form-group">
                    <label for="strain-name" class="col-form-label">Strain Name</label>
                    <input type="text" v-model="name" class="form-control" name="strain-name" id="strain-name" required>
                </div>
                <div class="form-group">
                    <label for="strain__type">Strain Type</label>
                    <select class="form-control" v-model.number="strainType" id="strain__type">
                        <option value="1">Indica</option>
                        <option value="2">Sativa</option>
                        <option value="3">Hybrid</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="strain-description" class="col-form-label">Description</label>
                    <vue-trix id="darkTrix" inputId="editor1" v-model="description" placeholder="Enter your content..."></vue-trix>
                </div>
                <button v-on:click.prevent="save" class="btn btn-primary">Add
                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
                <span v-if="success === true" class="text-success ml-1"> Strain added successfully.</span>
                <span v-else-if="success === false" class="text-danger ml-1"> Error adding strain.</span>
            </form>
        </vs-popup>
    </div>
</template>

<script>
    export default {
        name: 'AddStrain',
        data() {
            return {
                popupActivo: false,
                name: '',
                description: '',
                strainType: '',
                success: null,
                loading: false,
            }
        },
        methods: {
            save: function() {
                const data = {
                    name: this.name,
                    slug: this.name.trim().toLowerCase().replace(/ /g, '-').replace(/#/g, '').replace(/---/g, '-'),
                    description: this.description,
                    category_id: this.strainType,
                }

                this.loading = true;
                this.success = null;

                this.axios.post('/marijuana-strains', data)
                    .then((res) => {
                        this.success = true;
                        this.loading = false;
                        this.name = '';
                        this.description = '';
                        this.strainType = '';
                    })
                    .catch((e) => {
                        console.log(e.response)
                        this.success = false;
                        this.loading = false;
                    })
                    .finally(
                        setTimeout(() => {
                            this.success = null;
                        }, 2500)
                    );
            }
        }
    }
</script>

<style lang="scss" scoped>
    .btn-primary {
        padding: .3rem 1rem .2rem 1rem;
        min-width: 22%;
    }

    #strain__type {
        background: black;
        color: white;
        border: none;
        border-bottom: 1px solid white;
    }
    select.form-control:focus {
        box-shadow: none;
    }
</style>
