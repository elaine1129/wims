<template>
  <PageComponent title="View Warehouse">
    <ViewWarehouseHeader :warehouse="data.warehouse"></ViewWarehouseHeader>
    <br />
    <ViewUserTableComponent
      name="staffs"
      :data="data.staffs"
    ></ViewUserTableComponent>
    <div
      class="flex justify-center space-x-4"
      v-if="this.$store.getters.getUser.role == 'Admin'"
    >
      <Button
        type="success"
        size="large"
        ghost
        class="w-24"
        @click="showEditInventoryModal"
        >Edit</Button
      >
      <Button type="error" size="large" ghost class="w-24" disabled
        >Delete</Button
      >
    </div>
    <EditWarehouseModalTableComponent
      ref="editWarehouseModalComponent"
    ></EditWarehouseModalTableComponent>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import ViewWarehouseHeader from "../../components/view-warehouse-header.vue";
import ViewUserTableComponent from "../../components/tables/view-user-table.vue";
import EditWarehouseModalTableComponent from "../admin/components/modals/edit-warehouse-modal.vue";

export default {
  components: {
    PageComponent,
    ViewWarehouseHeader,
    ViewUserTableComponent,
    EditWarehouseModalTableComponent,
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
    await this.$axiosClient
      .get("/warehouse/" + this.$route.params.id)
      .then((response) => {
        this.data.warehouse = response.data.data;
        this.data.staffs = response.data.data.staffs;
        console.log(this.data.warehouse);
      })
      .catch((error) => {
        this.handleApiError(error);
      });
    // console.log("warehosue", res);
    // this.data.staffs = _.filter(res.data.data.staffs, { role: "Staff" });
    // console.log(this.data.staffs);
    $(document).ready(function () {
      $("#staffs").DataTable();
    });
  },
  methods: {
    showEditInventoryModal() {
      this.$refs.editWarehouseModalComponent.setProps(
        true,
        _.cloneDeep(this.data.warehouse)
      );
    },
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

