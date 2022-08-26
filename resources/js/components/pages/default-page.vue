<template>
  <header class="bg-white shadow">
    <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold text-gray-900">
        {{ title }}
      </h1>
    </div>
  </header>

  <main>
    <div class="max-w-full mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Replace with your content -->
      <div class="px-4 py-6 sm:px-0 h-screen">
        <ResetPasswordModalComponent
          ref="resetPasswordModal"
        ></ResetPasswordModalComponent>
        <slot></slot>
      </div>
      <!-- /End replace -->
    </div>
  </main>
</template>

<script>
import ResetPasswordModalComponent from "../../components/change-password-modal.vue";
export default {
  components: {
    ResetPasswordModalComponent,
  },
  props: {
    title: String,
  },
  created() {},
  mounted() {
    if (this.$store.getters.getUser.is_first_time_login == 1) {
      this.$refs.resetPasswordModal.setProps(true);
    }
  },
};
</script>

<style>
</style>