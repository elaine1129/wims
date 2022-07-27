<template>
  <Modal v-model="modal" title="Delete inventory">
    <h2>Are you sure to delete warehouse {{ selectedWarehouse.name }}</h2>
    <Alert type="warning" show-icon>
      This action is irreversible.
      <template #desc>
        All data related to this warehouse will be deleted as well. This
        includes staff, inventory, sku, reports, and stocks.
        <strong>Reassign staff to other warehouse if necessary.</strong>
      </template>
    </Alert>
    <template #footer>
      <Button @click="closeDeleteWarehouseModal">Cancel</Button>
      <Button type="error" @click="deleteWarehouse">Delete</Button>
    </template>
  </Modal>
</template>

<script>
import router from "../../../../router";

export default {
  data() {
    return {
      modal: false,
      selectedWarehouse: {
        id: "",
        name: "",
      },
    };
  },
  created() {},
  methods: {
    setProps(modal, selectedWarehouse) {
      this.modal = modal;
      this.selectedWarehouse = selectedWarehouse;
      console.log(this.selectedWarehouse);
    },
    closeDeleteWarehouseModal() {
      this.modal = false;
      this.selectedWarehouse = {
        name: "",
      };
    },
    async deleteWarehouse() {
      await this.$axiosClient
        .delete("/warehouse/" + this.selectedWarehouse.id)
        .then((response) => {
          this.success("Warehouse successfully deleted!");
          this.modal = false;
          console.log("route", this.$route);
          if (this.$route.name == "admin-manage-warehouse") {
            this.$emit("deleted", this.selectedWarehouse.id);
          } else {
            router.go(-1);
          }
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
  },
};
</script>