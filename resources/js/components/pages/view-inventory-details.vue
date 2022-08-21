<template>
  <PageHeader :hidden-breadcrumb="true" title="Inventory Info">
    <template #content>
      <DescriptionList :col="2">
        <Description term="ID " class="headerRow">{{
          data.selectedInventory.id
        }}</Description>
        <Description term="Quantity on hand " class="headerRow">{{
          data.selectedInventory.qty_on_hand
        }}</Description>
        <Description term="Name " class="headerRow">{{
          data.selectedInventory.name
        }}</Description>
        <Description term="Storage bin " class="headerRow">{{
          data.selectedInventory.storage_bin
            ? data.selectedInventory.storage_bin[0].bin_number
            : "-"
        }}</Description>
        <Description term="Warehouse " class="headerRow">{{
          data.selectedInventory.warehouse
            ? data.selectedInventory.warehouse.name
            : "-"
        }}</Description>
        <Description term="Created at " class="headerRow">{{
          data.selectedInventory.created_by
            ? data.selectedInventory.created_by
            : "-"
        }}</Description>
        <Description term="Cost per unit " class="headerRow">{{
          data.selectedInventory.cost_per_unit
        }}</Description>
        <Description term="Updated at " class="headerRow">{{
          data.selectedInventory.updated_by
            ? data.selectedInventory.updated_by
            : "-"
        }}</Description>
      </DescriptionList>
    </template>
  </PageHeader>
  <br />
  <StockTableComponent name="stocks" :data="data.stocks"></StockTableComponent>
  <div class="flex justify-center space-x-4">
    <Button
      type="success"
      size="large"
      ghost
      class="w-24"
      @click="showEditInventoryModal"
      >Edit</Button
    >
    <Button
      type="error"
      size="large"
      ghost
      class="w-24"
      @click="showDeleteInventoryModal"
      >Delete</Button
    >
  </div>
  <EditInventoryModalComponent
    ref="editInventoryModalComponent"
  ></EditInventoryModalComponent>
  <DeleteInventoryModalComponent
    ref="deleteInventoryModalComponent"
  ></DeleteInventoryModalComponent>
</template>

<script>
import StockTableComponent from "../tables/stock-table.vue";
import EditInventoryModalComponent from "../../pages/admin/components/edit-inventory-modal.vue";
import DeleteInventoryModalComponent from "../../pages/admin/components/delete-inventory-modal.vue";
export default {
  // props: {
  //   id: String,
  // },
  components: {
    StockTableComponent,
    EditInventoryModalComponent,
    DeleteInventoryModalComponent,
  },
  data() {
    return {
      data: {
        selectedInventory: {},
        stocks: [],
      },
    };
  },
  async created() {
    console.log(this.$route.params);
    await this.$axiosClient
      .get("/inventory/" + this.$route.params.id)
      .then((response) => {
        console.log(response);
        this.data.selectedInventory = response.data.data;
      })
      .catch((error) => {
        this.handleApiError(error);
      });
    await this.$axiosClient
      .get("/getStocksByInventory/" + this.$route.params.id)
      .then((response) => {
        console.log(response);
        this.data.stocks = response.data.data;
      })
      .catch((error) => {
        this.handleApiError(error);
      });
    $(document).ready(function () {
      $("#stocks").DataTable();
    });
  },
  methods: {
    showEditInventoryModal() {
      this.$refs.editInventoryModalComponent.setProps(
        true,
        this.data.selectedInventory
      );
    },
    showDeleteInventoryModal() {
      this.$refs.deleteInventoryModalComponent.setProps(
        true,
        this.data.selectedInventory
      );
    },
  },
};
</script>