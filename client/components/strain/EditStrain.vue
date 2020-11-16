<template>
    <div class="centerx pt-3">
        <div class="title">
            <h4 class="text-420">Edit Strain</h4>
        </div>
        <form v-on:submit.prevent="save" v-if="strain">
            <div class="form-group floating-label mt-4">
                <input type="text" :value="name" class="floating-input" name="name" placeholder=" " required>
                <label for="strain-name" class="col-form-label">Strain Name</label>
            </div>
            <div class="form-group floating-label mt-5">
                <select class="floating-select" name="category_id" :value="category_id" @change="changeCategory($event)">
                    <option value="1">Indica</option>
                    <option value="2">Sativa</option>
                    <option value="3">Hybrid</option>
                </select>
                <label :class="{focused: strain.category_id}">Strain Type</label>
            </div>
            <!-- <div class="form-group">
                <label class="col-form-label">Description</label>                
                <client-only>
                    <vue-trix id="darkTrix" v-model="strain.description" placeholder="Enter your content..."></vue-trix>                    
                </client-only>
            </div> -->
            <div class="text-center">
                <button v-on:click.prevent="save" class="btn btn-primary mr-2">Save
                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
</template>

<script>

    export default {
        name: 'EditStrain',
        props: ['from'],
        data() {
            return {
                strain: this.from,
                loading: false,
                name: this.from.name,
                category_id: this.from.category_id,
            }
        },
        methods: {
            save: function() {
                const params = {
                    name: this.name,
                    category_id: this.category_id,
                    slug: this.name.trim().toLowerCase().replace(/ /g, '-').replace(/#/g, '').replace(/---/g, '-'),
                    // description: this.strain.description,
                }
                this.loading = true;
                this.axios.put(`/marijuana-strains/${this.strain.id}`, params)
                    .then((res) => {
                        this.loading = false;
                        window.location.reload();
                    });
            },
            changeName(e) {
                this.name = e.target.value;
            },
            changeCategory(e) {
                this.category_id = e.target.value;
            },
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
    // Floating
    .floating-label {
        position: relative;
    }
    .floating-input {
        padding: 4px 2px !important;
        display: block;
        width: 100%;
        background-color: transparent;
        border: none;
        box-shadow: unset;
        color: #FFF !important;
        border-bottom: solid 1px #FFF !important;
        border-radius: 0 !important;
    }

    .floating-label label {
        position: absolute !important;
        pointer-events: none;
        left: 0px;
        top: 5px;
        transition: 0.2s ease all;
        -moz-transition: 0.2s ease all;
        -webkit-transition: 0.2s ease all;
    }

    .floating-input:not(:placeholder-shown) ~ label,
    .floating-input:focus ~ label,
    .floating-select ~ label.focused {
        top: -17px;
        color: gray;
    }
</style>
