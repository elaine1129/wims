<template>
  <PageComponent title="Manage Inventory">
    <Button
      type="primary"
      icon="ios-add-circle-outline"
      style="margin-bottom: 20px"
      @click="showAddInventoryModal"
      >Add</Button
    >
    <ViewInventoryTableComponent
      name="inventories"
      :data="data.inventories"
    ></ViewInventoryTableComponent>
    <Modal v-model="addInventoryModal" title="Add new inventory">
      <Form :model="addInventoryForm" :label-width="80">
        <FormItem label="Name">
          <Input
            v-model="addInventoryForm.name"
            placeholder="Enter inventory name"
          ></Input>
        </FormItem>
        <FormItem label="Warehouse">
          <Select v-model="addInventoryForm.warehouse_id">
            <Option
              v-for="warehouse in data.warehouses"
              :key="warehouse.id"
              :value="warehouse.id"
              >{{ warehouse.name }}</Option
            >
          </Select>
        </FormItem>
        <FormItem label="Quantity On Hand">
          <Input v-model="addInventoryForm.qty_on_hand" type="number"></Input>
        </FormItem>
        <FormItem label="Cost per unit">
          <Input v-model="addInventoryForm.cost_per_unit" type="number"></Input>
        </FormItem>
        <FormItem label="Category">
          <Select v-model="addInventoryForm.category_id">
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
        <Button @click="closeAddInventoryModal">Cancel</Button>
        <Button type="primary" @click="addNewInventory">Confirm</Button>
      </template>
    </Modal>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import ViewInventoryTableComponent from "../../components/pages/view-inventory-table.vue";

export default {
  components: {
    PageComponent,
    ViewInventoryTableComponent,
  },
  data() {
    return {
      data: {
        inventories: [],
        warehouses: [],
        categories: [],
      },
      newInventory: null,
      addInventoryModal: false,
      addInventoryForm: {
        name: "",
        warehouse_id: null,
        qty_on_hand: 0,
        cost_per_unit: 0.0,
        category_id: null,
      },
    };
  },
  async created() {
    const res = await this.$axiosClient.get("/inventories");
    console.log(res);
    this.data.inventories = res.data.data;
    $(document).ready(function () {
      $("#inventories").DataTable();
    });
  },
  methods: {
    async addNewInventory() {
      console.log(this.addInventoryForm);
      await this.$axiosClient
        .post("/inventory", this.addInventoryForm)
        .then((response) => {
          console.log(response);
          this.addInventoryForm = {
            name: "",
            warehouse: null,
            qty_on_hand: 0,
            cost_per_unit: 0.0,
            category: null,
          };
          this.success("Inventory Created!");
          this.addInventoryModal = false;
        })
        .catch((error) => {
          console.log(error.response);
          this.handleApiError(error);
        });
    },
    closeAddInventoryModal() {
      this.addInventoryForm = {
        name: "",
        warehouse: null,
        qty_on_hand: 0,
        cost_per_unit: 0.0,
        category: null,
      };
      this.addInventoryModal = false;
    },
    async showAddInventoryModal() {
      const warehouseRes = await this.$axiosClient.get("/warehouses");
      const categoryRes = await this.$axiosClient.get("/categories");
      if (warehouseRes.status == 200 && categoryRes.status == 200) {
        this.addInventoryModal = true;
        this.data.warehouses = warehouseRes.data.data;
        this.data.categories = categoryRes.data;
        console.log(warehouseRes);
        console.log(categoryRes);
      } else {
        this.smtgWentWrong();
      }
    },
  },
};
</script>