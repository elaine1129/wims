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
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="bin in data.storage_bins" :key="bin.bin_id">
              <td>{{ bin.bin_id }}</td>
              <td>{{ bin.bin_number }}</td>
              <td>{{ bin.category ? bin.category.name : "-" }}</td>
              <td>{{ bin.inventory ? bin.inventory.name : "-" }}</td>
              <td>
                <Button type="primary" @click="showEditInventoryModal(bin)"
                  >Edit Inventory</Button
                >
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <th></th>
              <th></th>
              <th>Category ID</th>
            </tr>
          </tfoot>
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
    <Modal v-model="editInventoryModal" title="Edit inventory">
      <Form :model="assignBinForm" :label-width="80">
        <FormItem label="Inventory">
          <Select
            v-model="selectedBin.inventory"
            clearable
            style="width: 200px"
          >
            <Option
              v-for="inventory in data.unassigned_inventories"
              :value="inventory"
              :key="inventory.id"
              >{{ inventory.name }}</Option
            >
          </Select>
        </FormItem>
      </Form>
      <template #footer>
        <Button @click="closeEditInventoryModal">Cancel</Button>
        <Button type="primary" @click="editInventory">Edit Inventory</Button>
      </template>
    </Modal>
    <Modal v-model="multiAssignBin" title="Multi Assign Bin">
      <Form :model="editInventoryForm" :label-width="80">
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
      <template #footer>
        <Button @click="closeMultiAssignBinModal">Cancel</Button>
        <Button type="primary" @click="multiAssign">Assign</Button>
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
        warehouse: null,
        staffs: [],
        storage_bins: [],
        categories: [],
        unassigned_inventories: [],
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
      editInventoryModal: false,
      selectedBin: {
        inventory: {
          id: "",
        },
        bin_id: "",
        bin_number: "",
        category: {
          id: "",
        },
      },
    };
  },
  async created() {
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
    $(document).ready(function () {
      $("#staffs").DataTable();
    });
    $(document).ready(function () {
      $("#storage-bins").DataTable({
        initComplete: function () {
          this.api()
            .columns([2])
            .every(function () {
              var column = this;
              var select = $('<select><option value=""></option></select>')
                .appendTo($(column.footer()).empty())
                .on("change", function () {
                  var val = $.fn.dataTable.util.escapeRegex($(this).val());

                  column.search(val ? "^" + val + "$" : "", true, false).draw();
                });

              column
                .data()
                .unique()
                .sort()
                .each(function (d, j) {
                  select.append('<option value="' + d + '">' + d + "</option>");
                });
            });
        },
      });
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
          this.success("Multiple assignments successful!");
          this.multiAssignBin = false;
          this.assignBinForm = {
            category: {
              id: "",
              name: "",
            },
            storage_bins: [],
          };
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
    async showEditInventoryModal(bin) {
      await this.$axiosClient
        .get("/inventories-unassigned-category")
        .then((response) => {
          this.data.unassigned_inventories = response.data.data;
        })
        .catch((error) => {
          this.handleApiError(error);
        });
      this.editInventoryModal = true;
      this.selectedBin = _.cloneDeep(bin);
      if (this.selectedBin.inventory == null) {
        this.selectedBin.inventory = {
          id: "",
        };
      } else {
        this.data.unassigned_inventories.push(this.selectedBin.inventory);
      }
      console.log(bin);
    },
    closeEditInventoryModal() {
      this.editInventoryModal = false;
      this.selectedBin = {
        inventory: {
          id: "",
        },
        bin_id: "",
        bin_number: "",
        category: {
          id: "",
        },
      };
    },
    async editInventory() {
      console.log(this.selectedBin);
      let param = {
        bin_id: this.selectedBin.bin_id,
        category_id: this.selectedBin.category.id,
        inventory_id: this.selectedBin.inventory
          ? this.selectedBin.inventory.id
          : null,
      };
      await this.$axiosClient
        .post("/storage-bin-edit-inventory/" + this.data.warehouse.id, param)
        .then((response) => {
          console.log(response);
          this.success(
            `The inventory for stoage bin ${this.selectedBin.bin_number} has been successfully updated!`
          );
          this.closeEditInventoryModal();
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

