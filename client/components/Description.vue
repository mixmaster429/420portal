<template>
    <div class="edit__box mt-3">
        <client-only>
            <vue-trix v-model.lazy="description" class="mb-2" v-if="editMode" inputId="edit_description" placeholder="Enter your content..."/>
        </client-only>
        
        <div v-if="!editMode" class="category__show">
            <div v-html="description"></div>
        </div>

        <div v-if="auth_user && auth_user.id == 1" class="strain__description__edit text-right mb-4">
            <div v-if="editMode" class="edit-mode mt-2">
                <button @click="save" class="btn btn-sm btn-primary px-3 py-2"><fa :icon="['far', 'save']" class="mr-1"></fa> Save
                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
                <button @click="cancel" class="btn btn-sm btn-outline-secondary px-3 py-2"><fa icon="times" class="mr-1"></fa> Cancel</button>
            </div>
            <div v-else class="edit-mode mt-2">
                <button @click="editMode = true" class="btn btn-sm btn-primary px-3 py-2 mt-2"><fa :icon="['far', 'edit']" class="mr-1"></fa> Edit</button>
            </div>
            <div v-if="success == 1" class="text-success small mt-2">
                Description changed successfully
            </div>
            <div v-else-if="success == 2" class="text-danger small mt-2">
                Error editing description
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    name: "Description",
    props: ["field_name"],
    serverPrefetch() {
        return this.getDescription();
    },
    computed: {
        ...mapGetters({
            auth_user: 'auth/user',
        }),        
        description: {
            get() {
                return this.$store.state.prefetch.description;
            },
            set(data) {
                this.$store.commit('prefetch/SET_DESCRIPTION', data);
            }
        }
    },
    data() {
        return {
            editMode: false,
            latest: '',
            loading: false,
            success: 0
        };
    },
    mounted() {
        if(!this.description) {
            this.getDescription();
        }
        this.latest = this.description;
    },
    methods: {
        save() {
            this.loading = true;
            const params = {
                field_name: this.field_name,
                description: this.description
            };
            let url = '/update_description';
            this.axios.post(url, params).then(response => {
                this.editMode = false;
                this.success = 1;
                this.loading = false;
                this.latest = response.data.description;
            });
        },
        getDescription() {
            return this.$store.dispatch('prefetch/getDescription', this.field_name);
        },
        cancel() {
            this.description = this.latest;
            this.editMode = false;
        }
    }
};
</script>