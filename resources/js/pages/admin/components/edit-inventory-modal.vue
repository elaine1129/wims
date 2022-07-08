<template>
  <Modal v-model="modal" title="Edit inventory">
    <Form :model="editInventoryForm" :label-width="80">
      <FormItem label="ID">
        <Input :disabled="true" v-model="editInventoryForm.id"></Input>
      </FormItem>
      <FormItem label="Name">
        <Input
          v-model="editInventoryForm.name"
          placeholder="Enter inventory name"
        ></Input>
      </FormItem>
      <FormItem label="Warehouse">
        <Input
          :disabled="true"
          v-model="editInventoryForm.warehouse.name"
          placeholder="Enter warehouse name"
        ></Input>
      </FormItem>
      <FormItem label="Quantity On Hand">
        <Input
          :disabled="true"
          v-model="editInventoryForm.qty_on_hand"
          type="number"
        ></Input>
      </FormItem>
      <FormItem label="Cost per unit">
        <Input v-model="editInventoryForm.cost_per_unit" type="number"></Input>
      </FormItem>
      <FormItem label="Category">
        <Select v-model="editInventoryForm.category.id">
          <Option
            v-for="category in data.categories"
            :key="category.id"
            :value="category.id"
            >{{ category.name }}</Option
          >
        </Select>
      </FormItem>
    </Form>

    <template #footer>
      <Button @click="closeEditInventoryModal">Cancel</Button>
      <Button type="primary" @click="updateInventory">Confirm</Button>
    </template>
  </Modal>
</template>

<script>
export default {
  data() {
    return {
      data: {
        categories: [],
      },
      modal: false,
      editInventoryForm: {
        id: "",
        name: "",
        warehouse: {
          name: "",
        },
        qty_on_hand: 0,
        cost_per_unit: 0,
        storage_bin: "",
        category: {
          id: "",
        },
      },
    };
  },
  async created() {
    await this.$axiosClient
      .get("/categories")
      .then((response) => {
        console.log(response);
        this.data.categories = response.data;
      })
      .catch((error) => {
        this.handleApiError(error);
      });
  },
  methods: {
    setProps(modal, selectedInventory) {
      this.modal = modal;
      this.editInventoryForm = selectedInventory;
      console.log(selectedInventory);
    },
    closeEditInventoryModal() {
      this.modal = false;
    },

    async updateInventory() {
      let params = {
        name: this.editInventoryForm.name,
        cost_per_unit: this.editInventoryForm.cost_per_unit,
        // storage_bin: "",
        category_id: this.editInventoryForm.category.id,
      };
      await this.$axiosClient
        .put("/inventory/" + this.editInventoryForm.id, params)
        .then((response) => {
          this.editInventoryForm = {
            id: "",
            name: "",
            warehouse: {
              name: "",
            },
            qty_on_hand: 0,
            cost_per_unit: 0,
            storage_bin: "",
            category: {
              id: "",
            },
          };
          this.success("Inventory successfully updated");
          this.closeEditInventoryModal();
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
  },
};
</script>