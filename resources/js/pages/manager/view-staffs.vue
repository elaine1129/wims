<template>
  <PageComponent title="View Staffs">
    <ViewWarehouseHeader :warehouse="data.warehouse"></ViewWarehouseHeader>
    <br />
    <ViewUserTableComponent
      name="staffs"
      :data="data.staffs"
    ></ViewUserTableComponent>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import ViewWarehouseHeader from "../../components/view-warehouse-header.vue";
import ViewUserTableComponent from "../../components/tables/view-user-table.vue";
export default {
  components: {
    PageComponent,
    ViewWarehouseHeader,
    ViewUserTableComponent,
  },
  data() {
    return {
      data: {
        // staffs: [],
        // userData: null,
        warehouse: null,
        staffs: [],
      },
      selectedStaff: null,
    };
  },
  async created() {
    // this.data.userData = this.$store.getters.getUser;
    // console.log(this.data.userData);
    const res = await this.$axiosClient.get(
      "/warehouse/" + this.$store.getters.getUser.warehouse_id
    );
    console.log("warehosue", res);
    this.data.warehouse = res.data.data;
    this.data.staffs = _.filter(res.data.data.staffs, { role: "Staff" });
    console.log(this.data.staffs);
    $(document).ready(function () {
      $("#staffs").DataTable();
    });
  },
};
</script>

<style>
.headerRow .ivu-description-term {
  min-width: 150px;
}

.user-details-label {
  font-weight: bold;
}
</style>

