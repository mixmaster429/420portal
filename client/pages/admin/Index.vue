<template>
    <div class="row">
        <div class="col-12">
            <div class="container">
                <div class="row mt-5">
                    <div class="col-md-3">
                        <div class="card settings-card">
                            <div class="card-header"><h4 class="mb-0">Admin Panel</h4></div>
                            <div class="card-body">
                                <ul class="nav flex-column nav-pills">
                                    <li v-for="tab in tabs" :key="tab.route" class="nav-item">
                                        <router-link :to="{ name: tab.route, params: tab.params }" class="nav-link" active-class="active">
                                            {{ tab.name }}
                                        </router-link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <transition name="fade" mode="out-in">
                                <router-view />
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</template>

<script>
export default {
  middleware: 'auth',
  computed: {
    tabs () {
        return [
            {
                name: 'All Posts',
                route: 'admin.post'
            },
            {
                name: 'Add Post',
                route: 'admin.post_form',
                params: {mode: 'add'},
            },
            {
                name: 'Category',
                route: 'admin.category',
            },
        ]
    }
  }
}
</script>

<style lang="scss" scoped>
    .settings-card .card-body {
        padding: 0;
    }
    .nav-pills {
        .nav-link {
            &.active {
                background-color: #000;
                color: #EFA720;
            }
        }
    }
</style>
