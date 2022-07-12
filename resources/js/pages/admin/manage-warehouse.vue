<template>
  <PageComponent title="Manage Warehouse">
    <Button
      type="primary"
      icon="ios-add-circle-outline"
      style="margin-bottom: 20px"
      @click="showAddWarehouseModal"
      >Add</Button
    >
    <table id="warehouses" class="display" style="width: 100%">
      <thead>
        <tr>
          <th>Warehouse ID</th>
          <th>Name</th>
          <th>Location</th>
          <th>Warehouse Manager</th>
          <th>Created at</th>
          <th>Updated at</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="warehouse in data.warehouses" :key="warehouse.id">
          <td>
            <router-link
              :to="{
                name: 'view-warehouse-details',
                params: { id: warehouse.id },
              }"
              >{{ warehouse.id }}</router-link
            >
          </td>
          <td>{{ warehouse.name }}</td>
          <td>{{ warehouse.location }}</td>
          <td>{{ warehouse.manager ? warehouse.manager.name : "-" }}</td>
          <td>{{ warehouse.created_at ? warehouse.created_at : "-" }}</td>
          <td>{{ warehouse.updated_at ? warehouse.updated_at : "-" }}</td>
          <td v-if="this.$store.getters.getUser.role == 'Admin'">
            <div class="flex items-center gap-x-3">
              <Button
                type="success"
                class="basis-1/2"
                @click="showEditInventoryModal(warehouse)"
                >Edit</Button
              >
              <Button type="error" class="basis-1/2" disabled>Delete</Button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <Modal v-model="addWarehouseModal" title="Add new warehouse">
      <Form
        :model="addWarehouseForm"
        :label-width="80"
        ref="addWarehouseForm"
        :rules="addWarehouseFormValidate"
      >
        <FormItem label="Name">
          <Input
            v-model="addWarehouseForm.name"
            placeholder="Enter warehouse name"
          ></Input>
        </FormItem>

        <FormItem label="Location">
          <Input
            v-model="addWarehouseForm.location"
            type="textarea"
            placeholder="Enter warehouse location"
          ></Input>
        </FormItem>
        <FormItem label="Number of Bins" prop="no_of_bins">
          <Input
            v-model="addWarehouseForm.no_of_bins"
            type="number"
            placeholder="Enter number of bins"
          ></Input>
        </FormItem>
        <FormItem label="Zones" prop="zones">
          <Input
            v-model="addWarehouseForm.zones"
            placeholder="Enter all zones separated by comma(eg. A,B,C,D,E)"
          ></Input>
        </FormItem>
      </Form>
      <template #footer>
        <Button @click="closeAddWarehouseModal">Cancel</Button>
        <Button type="primary" @click="handleSubmit('addWarehouseForm')"
          >Confirm</Button
        >
      </template>
    </Modal>

    <EditWarehouseModalTableComponent
      ref="editWarehouseModalComponent"
    ></EditWarehouseModalTableComponent>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import EditWarehouseModalTableComponent from "../admin/components/modals/edit-warehouse-modal.vue";
export default {
  components: {
    PageComponent,
    EditWarehouseModalTableComponent,
  },
  data() {
    return {
      data: {
        warehouses: [],
        categories: [],
      },
      addWarehouseModal: false,
      addWarehouseForm: {
        name: "",
        location: "",
        no_of_bins: 0,
        zones: "",
      },
      selectedWarehouse: null,

      addWarehouseFormValidate: {
        no_of_bins: [
          {
            required: true,
            message: "Number of bins cannot be empty",
            trigger: "blur",
          },
          {
            validator: this.validateNumberOfBin,
            trigger: "blur",
            type: "number",
            transform(value) {
              return Number(value);
            },
          },
        ],
        zones: [
          {
            required: true,
            message: "Please enter at least one zone category",
            trigger: "blur",
          },
        ],
      },
    };
  },
  async created() {
    await this.$axiosClient
      .get("/warehouses")
      .then((response) => {
        console.log(response.data.data);
        this.data.warehouses = response.data.data;
      })
      .catch((error) => {
        this.handleApiError(error);
      });
    // console.log(res);
    // this.data.inventories = res.data.data;
    $(document).ready(function () {
      $("#warehouses").DataTable();
    });
  },
  methods: {
    validateNumberOfBin(rule, value, callback) {
      if (this.addWarehouseForm.no_of_bins < this.data.categories.length) {
        console.log(this.addWarehouseForm.no_of_bins);
        return callback(
          new Error(
            `Please enter at least ${this.data.categories.length}  bins`
          )
        );
      }
      callback();
    },
    async handleSubmit(name) {
      await this.$axiosClient
        .get("/categories")
        .then((response) => {
          this.data.categories = response.data;
        })
        .catch((error) => {
          this.handleApiError(error);
        });
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.addNewWarehouse();
        } else {
          this.warning("There's something wrong with the input");
        }
      });
    },
    async addNewWarehouse() {
      this.createStorageBins();
      let params = {
        name: this.addWarehouseForm.name,
        location: this.addWarehouseForm.location,
        storage_bins: this.createStorageBins(),
      };
      await this.$axiosClient
        .post("/warehouse", params)
        .then((response) => {
          console.log(response);
          this.addWarehouseForm = {
            name: "",
            location: "",
            no_of_bins: 0,
            zones: "",
          };
          this.success("Warehouse Created!");
          this.addWarehouseModal = false;
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
    closeAddWarehouseModal() {
      this.addWarehouseForm = {
        name: "",
        location: "",
      };
      this.addWarehouseModal = false;
    },
    showAddWarehouseModal() {
      this.addWarehouseModal = true;
    },
    createStorageBins() {
      let storage_bins = [];
      let zones = _.chain(this.addWarehouseForm.zones)
        .split(",")
        .map(function (zone) {
          return _.capitalize(_.trim(zone));
        })
        .value();
      let counter = 1;
      let no_of_bins_per_zone = Math.floor(
        this.addWarehouseForm.no_of_bins / zones.length
      );
      let no_of_bins_per_category = Math.floor(
        this.addWarehouseForm.no_of_bins / this.data.categories.length
      );
      for (let i = 0; i < zones.length; i++) {
        for (let j = 1; j <= no_of_bins_per_zone; j++) {
          let obj = {
            bin_id: counter,
            bin_number: zones[i] + j,
            category_id: "",
            inventory_id: "",
          };
          storage_bins.push(obj);
          counter++;
        }
      }
      let start = no_of_bins_per_zone + 1;
      while (counter <= this.addWarehouseForm.no_of_bins) {
        let obj = {
          bin_id: counter,
          bin_number: zones[zones.length - 1] + start,
          category_id: "",
          inventory_id: "",
        };
        storage_bins.push(obj);
        counter++;
        start++;
      }
      let index;
      let iteration = 0;
      for (index = 0; index < this.data.categories.length; index++) {
        for (let i = 0; i < no_of_bins_per_category; i++) {
          storage_bins[iteration].category_id = this.data.categories[index].id;
          iteration++;
        }
      }
      while (iteration < storage_bins.length) {
        storage_bins[iteration].category_id =
          this.data.categories[this.data.categories.length - 1].id;
        iteration++;
      }
      return JSON.stringify(storage_bins);
    },
    showEditInventoryModal(warehouse) {
      this.selectedWarehouse = warehouse;
      this.$refs.editWarehouseModalComponent.setProps(
        true,
        this.selectedWarehouse
      );
    },
  },
};
</script>