<template>
  <Modal
    v-model="modal"
    title="Please reset your password before proceed"
    :closable="false"
    :mask-closable="false"
  >
    <Form :model="changePwForm" :label-width="80">
      <FormItem label="New password">
        <Input
          v-model="changePwForm.new_password"
          type="password"
          password
          placeholder="Enter new password"
        ></Input>
      </FormItem>
      <FormItem label="Confirm password">
        <Input
          v-model="changePwForm.confirm_password"
          type="password"
          password
          placeholder="Enter confirm password"
        ></Input>
      </FormItem>
    </Form>
    <h2>Kindly take note that the password cannot be changed anymore</h2>
    <template #footer>
      <Button type="primary" @click="changePassword">OK</Button>
    </template>
  </Modal>
</template>

<script>
export default {
  data() {
    return {
      modal: false,
      changePwForm: {
        new_password: "",
        confirm_password: "",
      },
    };
  },
  created() {},
  methods: {
    setProps(modal) {
      this.modal = modal;
    },
    async changePassword() {
      console.log(this.changePwForm);
      await this.$axiosClient
        .post("/reset-password", this.changePwForm)
        .then((response) => {
          console.log(response);
          this.success("The password has been successfully reset.");
          this.$store.commit("setFirstTimeLogin", response.data);
          this.modal = false;
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
  },
};
</script>