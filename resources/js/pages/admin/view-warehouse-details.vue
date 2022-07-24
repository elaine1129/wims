<template>
  <PageComponent title="View Warehouse">
    <ViewWarehouseHeader :warehouse="data.warehouse"></ViewWarehouseHeader>
    <br />
    <Tabs type="card">
      <TabPane label="Staffs">
        <ViewUserTableComponent
          name="staffs"
          :data="data.staffs"
        ></ViewUserTableComponent>
      </TabPane>
      <TabPane label="Storage Bins">
        <Button
          type="primary"
          icon="ios-add-circle-outline"
          style="margin-bottom: 20px"
          @click="showMultiAssignBinModal"
          >Multi Assign Bin</Button
        >
        <table id="storage-bins" class="display" style="width: 100%">
          <thead>
            <tr>
              <th>Bin ID</th>
              <th>Bin Number</th>
              <th>Category ID</th>
              <th>Inventory ID</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="bin in data.storage_bins" :key="bin.bin_id">
              <td>{{ bin.bin_id }}</td>
              <td>{{ bin.bin_number }}</td>
              <td>{{ bin.category ? bin.category.name : "-" }}</td>
              <td>{{ bin.inventory ? bin.inventory.name : "-" }}</td>
            </tr>
          </tbody>
        </table>
      </TabPane>
    </Tabs>

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
    <Modal
      v-model="multiAssignBin"
      title="Multi Assign Bin"
      @on-ok="multiAssignCategoryToBin"
      @on-cancel="closeMultiAssignBinModal"
    >
      <Form :model="assignBinForm" :label-width="80">
        <FormItem label="Category">
          <Select v-model="assignBinForm.category" style="width: 200px">
            <Option
              v-for="category in data.categories"
              :value="category"
              :key="category.id"
              >{{ category.name }}</Option
            >
          </Select>
        </FormItem>
        <FormItem label="Storage Bins">
          <div
            style="
              border-bottom: 1px solid #e9e9e9;
              padding-bottom: 6px;
              margin-bottom: 6px;
            "
          >
            <Checkbox
              :indeterminate="bin_indeterminate"
              :model-value="bin_checkAll"
              @click.prevent="binHandleCheckAll"
              >Select All</Checkbox
            >
          </div>
          <CheckboxGroup
            v-model="assignBinForm.storage_bins"
            @on-change="binCheckAllGroupChange"
          >
            <Checkbox
              v-for="bin in data.storage_bins"
              :key="bin.bin_id"
              :label="bin.bin_number"
            >
            </Checkbox>
          </CheckboxGroup>
        </FormItem>
      </Form>
    </Modal>

    <Modal v-model="confirmMultiAssignModal" title="Are you sure?">
      <h2>
        Are you sure to multiassign the selected bins to the category "{{
          this.assignBinForm.category.name
        }}"?
      </h2>
      <template #footer>
        <Button @click="confirmMultiAssignModal = false">Cancel</Button>
        <Button type="default" @click="multiAssign">Assign</Button>
      </template>
    </Modal>
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
        storage_bins: [],
        categories: [],
      },
      selectedStaff: null,
      multiAssignBin: false,
      bin_indeterminate: true,
      bin_checkAll: false,
      assignBinForm: {
        category: {
          id: "",
          name: "",
        },
        storage_bins: [],
      },
      confirmMultiAssignModal: false,
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
        this.data.storage_bins = response.data.data.storage_bins;
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
    $(document).ready(function () {
      $("#storage-bins").DataTable();
    });
  },
  methods: {
    showEditInventoryModal() {
      this.$refs.editWarehouseModalComponent.setProps(
        true,
        _.cloneDeep(this.data.warehouse)
      );
    },
    async showMultiAssignBinModal() {
      await this.$axiosClient
        .get("/categories")
        .then((response) => {
          console.log(response);
          this.data.categories = response.data;
        })
        .catch((error) => {
          this.handleApiError(error);
        });
      this.multiAssignBin = true;
    },
    multiAssignCategoryToBin() {
      console.log(this.assignBinForm);
      this.confirmMultiAssignModal = true;
    },
    closeMultiAssignBinModal() {
      this.multiAssignBin = false;
    },
    binHandleCheckAll() {
      if (this.bin_indeterminate) {
        this.bin_checkAll = false;
      } else {
        this.bin_checkAll = !this.bin_checkAll;
      }
      this.bin_indeterminate = false;

      if (this.bin_checkAll) {
        this.assignBinForm.storage_bins = _.map(
          this.data.storage_bins,
          (bin) => {
            return bin.bin_number;
          }
        );
      } else {
        this.assignBinForm.storage_bins = [];
      }
    },
    binCheckAllGroupChange(data) {
      if (data.length === this.data.storage_bins.length) {
        this.bin_indeterminate = false;
        this.bin_checkAll = true;
      } else if (data.length > 0) {
        this.bin_indeterminate = true;
        this.bin_checkAll = false;
      } else {
        this.bin_indeterminate = false;
        this.bin_checkAll = false;
      }
    },
    async multiAssign() {
      var param = {
        category_id: this.assignBinForm.category.id,
        storage_bins: this.assignBinForm.storage_bins,
      };

      await this.$axiosClient
        .post("/assign-category-to-bin/" + this.data.warehouse.id, param)
        .then((response) => {
          console.log(response);
        })
        .catch((error) => {
          this.handleApiError(error);
        });
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

