<template>
  <PageComponent title="View Inventory">
    <ViewInventoryTableComponent
      name="inventories"
      :data="data.inventories"
    ></ViewInventoryTableComponent>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import ViewInventoryTableComponent from "./components/view-inventory-table.vue";
export default {
  components: {
    PageComponent,
    ViewInventoryTableComponent,
  },
  data() {
    return {
      data: {
        inventories: [],
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
};
</script>