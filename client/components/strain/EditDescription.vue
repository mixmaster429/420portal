<template>
    <div class="edit__box">
        <VueTrix v-model.lazy="desc" id="darkTrix" class="mb-2" v-if="editMode" inputId="editor2" placeholder="Enter your content..."/>
        <div v-else class="category__show">
            <div v-html="desc"></div>
        </div>

        <div v-if="auth" class="strain__description__edit text-right mb-4">
            <div v-if="editMode" class="edit-mode mt-2">
                <button v-on:click="save" class="btn btn-primary px-3 py-2"><i class="far fa-save mr-1"></i> Save
                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
                <button v-on:click="cancel" class="btn btn-outline-secondary px-3 py-2"><i class="fas fa-times mr-1"></i> Cancel</button>
            </div>
            <div v-else class="edit-mode mt-2">
                <button v-on:click="deleteItem" v-if="this.type == 'strain'" class="btn btn-outline-secondary px-3 py-2 mt-2 mr-2"><i class="far fa-trash-alt mr-1"></i> Delete</button>
                <button v-on:click="editMode = true" class="btn btn-primary px-3 py-2 mt-2"><i class="far fa-edit mr-1"></i> Edit</button>
            </div>
            <div v-if="success === 1" class="text-success small mt-2">
                Description changed successfully
            </div>
            <div v-else-if="success === 2" class="text-danger small mt-2">
                Error editing description
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "EditDescription",
    props: ["strain", "auth", "type"],
    components: {
        // VueTrix
    },
    data() {
        return {
            editMode: false,
            desc: this.strain.description,
            latest: '',
            loading: false,
            success: 0
        };
    },
    methods: {
        save() {
            this.loading = true;

            const data = {
                description: this.desc
            };

            let param = '';

            switch (this.type) {
                case 'category':
                    param = `/category/${this.strain.id}`
                    break;

                case 'strain':
                    param = `/marijuana-strains/${this.strain.id}`
                    break;

                case 'strain-modal':
                    param = `/marijuana-strains/modal/${this.strain.id}`
                    break;

                default:
                break;
            }

            axios.put(param, data)
                .then(res => {
                    console.log(res.data);
                    this.editMode = false;
                    this.success = 1;
                    this.loading = false;
                    this.latest = res.data;
                    console.log('the latest data is', this.latest)
                })
                .catch(e => {
                    console.log(e.response);
                    this.success = 2;
                    this.loading = false
                    console.log(this.success);
                })
                .finally(setTimeout( ()=> {
                    this.success = null;
                }, 2500));
        },
        cancel() {
            this.desc = this.latest ? this.latest.description : this.strain.description;
            this.editMode = false;
        },
        deleteItem() {
            if(!window.user) {
                $("loginmodal").modal('show');
                return false;
            } else {
                if(window.confirm('Are you sure?')) {
                    let url = `/marijuana-strains/${this.strain.id}`;
                    axios.delete(url)
                        .then(response => {
                            console.log(respnse.data)
                            if(response.data.status == 200) {
                                window.location.href = '/';
                            }
                        });
                }
            }
        }
    }
};
</script>