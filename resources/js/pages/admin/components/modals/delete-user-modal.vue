<template>
  <Modal v-model="modal" title="Delete user">
    <h2>
      Are you sure to delete user {{ selectedUser.id }} from
      {{ selectedUser.warehouse.name }} ?
    </h2>
    <template #footer>
      <Button @click="closeDeleteUserModal">Cancel</Button>
      <Button type="error" @click="deleteUser">Delete</Button>
    </template>
  </Modal>
</template>

<script>
import router from "../../../../router";

export default {
  data() {
    return {
      modal: false,
      selectedUser: {
        id: "",
        warehouse: {
          name: "",
        },
      },
    };
  },
  created() {},
  methods: {
    setProps(modal, selectedUser) {
      this.modal = modal;
      this.selectedUser = selectedUser;
      console.log(this.selectedUser);
    },
    closeDeleteUserModal() {
      this.modal = false;
      this.selectedUser = {
        id: "",
        warehouse: {
          name: "",
        },
      };
    },
    async deleteUser() {
      await this.$axiosClient
        .delete("/user/" + this.selectedUser.id)
        .then((response) => {
          this.success("User successfully deleted!");
          this.modal = false;
          if (this.$route.name == "admin-manage-user") {
            this.$emit("deleted");
          } else {
            router.go(-1);
          }
        })
        .catch((error) => {
          console.log();
          if (error.response.status == 501) {
            this.error(error.response.data.errors[0]);
          }
          this.handleApiError(error);
        });
    },
  },
};
</script>