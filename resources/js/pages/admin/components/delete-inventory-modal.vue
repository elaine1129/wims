<template>
  <Modal v-model="modal" title="Delete inventory">
    <h2>
      Are you sure to delete inventory {{ selectedInventory.id }} from
      {{ selectedInventory.warehouse.name }} ?
    </h2>
    <template #footer>
      <Button @click="closeDeleteInventoryModal">Cancel</Button>
      <Button type="error" @click="deleteInventory">Delete</Button>
    </template>
  </Modal>
</template>

<script>
import router from "../../../router";

export default {
  data() {
    return {
      modal: false,
      selectedInventory: {
        id: "",
        warehouse: {
          name: "",
        },
      },
    };
  },
  created() {},
  methods: {
    setProps(modal, selectedInventory) {
      this.modal = modal;
      this.selectedInventory = selectedInventory;
      console.log(this.selectedInventory);
    },
    closeDeleteInventoryModal() {
      this.modal = false;
      this.selectedInventory = {
        id: "",
        warehouse: {
          name: "",
        },
      };
    },
    async deleteInventory() {
      await this.$axiosClient
        .delete("/inventory/" + this.selectedInventory.id)
        .then((response) => {
          this.success("Inventory successfully deleted!");
          this.modal = false;
          router.go(-1);
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
  },
};
</script>