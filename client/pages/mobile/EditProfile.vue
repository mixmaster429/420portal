<template>
    <div>
        <div
            class="sticky-top strains__sticky"
            style="margin-right: -15px; margin-left: -15px; background: #fff;width:unset;"
        >
            <ul class="nav strains__nav">
                <p style="margin: auto 5px; padding: 8px; font-size: 20px;">
                    <fa @click="$router.go(-1)" icon="times" class="mr-2"></fa> Edit Profile
                </p>
                <div class="ml-auto" style="padding: 8px; font-size: 20px;">
                <fa
                    @click.prevent="$root.$emit('edit-profile')"
                    icon="check"
                    class="mr-3 text-420"
                ></fa>
                </div>
            </ul>
        </div>
        <div class="media__add" style="margin-top: 2rem;margin-bottom: 5rem;">
            <ProfileForm :editData="editData" :mainData="mainData" mode="edit"></ProfileForm>
        </div>
    </div>
</template>

<script>
import ProfileForm from "~/components/ProfileForm";

export default {
  name: "EditProfile",
  components: {
    ProfileForm
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

<style lang="scss" scoped>
.media__add {
  color: #fff;
}
</style>