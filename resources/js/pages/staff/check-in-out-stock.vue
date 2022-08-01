<template>
  <!-- <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold text-gray-900">Check in and out stock</h1>
    </div>
  </header> -->
  <!-- <main> -->
  <!-- <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8"> -->
  <!-- Replace with your content -->
  <!-- <div class="px-4 py-6 sm:px-0"> -->
  <PageComponent title="Check In/ Out Stock">
    <div class="_box_shadow _border_radious _mar_b30 _p20">
      <table id="inventories" class="display" style="width: 100%">
        <thead>
          <tr>
            <th>Inventory ID</th>
            <th>Name</th>
            <th>Cost per Unit</th>
            <th>Quantity On Hand</th>
            <th>Storage Bin Number</th>
            <th>Category ID</th>
            <th>Created By</th>
            <th>Updated By</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="inventory in data.inventories" :key="inventory.id">
            <td>{{ inventory.id }}</td>
            <td>{{ inventory.name }}</td>
            <td>{{ inventory.cost_per_unit }}</td>
            <td>{{ inventory.qty_on_hand }}</td>
            <td>
              {{
                inventory.storage_bin[0]
                  ? inventory.storage_bin[0].bin_number
                  : "-"
              }}
            </td>
            <td>{{ inventory.category ? inventory.category.name : "-" }}</td>
            <td>{{ inventory.created_by }}</td>
            <td>{{ inventory.updated_by }}</td>
            <td>
              <Button type="primary" @click="openCheckInOutModal(inventory.id)"
                >Check In/Out Stock</Button
              >
            </td>
          </tr>
        </tbody>
      </table>

      <Modal v-model="checkInOutModal">
        <template #header>
          <p>Check In/ Out Inventory - {{ checkInOutModalInv.name }}</p>
        </template>
        <div class="row">
          <p class="col-6">Inventory ID:</p>
          <p class="col-6">
            {{ checkInOutModalInv.id }}
          </p>
        </div>
        <div class="row">
          <p class="col-6">Quantity On Hand:</p>
          <p class="col-6">
            {{ checkInOutModalInv.qty_on_hand }}
          </p>
        </div>
        <Tabs type="card" @on-click="handleTabClickingEvent">
          <TabPane label="Check In" name="checkin">
            <Form :model="checkInForm" :label-width="80">
              <FormItem label="Quantity">
                <Input
                  class="check-in-out-stock-quantity-input"
                  v-model="checkInForm.quantity"
                  type="number"
                  placeholder="Enter quantity"
                ></Input>
              </FormItem>
              <FormItem label="Remarks">
                <Input
                  class="check-in-out-stock-remarks-input"
                  v-model="checkInForm.remarks"
                  type="textarea"
                  :autosize="{ minRows: 2, maxRows: 5 }"
                  :show-word-limit="true"
                  maxlength="191"
                  placeholder="Enter remarks"
                ></Input>
              </FormItem>
            </Form>
          </TabPane>
          <TabPane label="Check Out" name="checkout">
            <Form :model="checkOutForm" :label-width="80">
              <FormItem label="Quantity">
                <Input
                  class="check-in-out-stock-quantity-input"
                  v-model="checkOutForm.quantity"
                  type="number"
                  placeholder="Enter quantity"
                ></Input>
              </FormItem>
              <FormItem label="Remarks">
                <Input
                  class="check-in-out-stock-remarks-input"
                  v-model="checkOutForm.remarks"
                  type="textarea"
                  :autosize="{ minRows: 2, maxRows: 5 }"
                  :show-word-limit="true"
                  maxlength="191"
                  placeholder="Enter remarks"
                ></Input>
              </FormItem>
            </Form>
          </TabPane>
        </Tabs>
        <template #footer class="d-flex justify-content-center">
          <Button>Cancel</Button>
          <Button type="primary" @click="checkInOutStock">Confirm</Button>
        </template>
      </Modal>
    </div>
  </PageComponent>
  <!-- </div> -->

  <!-- /End replace -->
  <!-- </div> -->
  <!-- </main> -->
</template>

<script>
</script>

<script>
import "jquery/dist/jquery.min.js";
//Datatable Modules
import "datatables.net-dt/js/dataTables.dataTables";
import "datatables.net-dt/css/jquery.dataTables.min.css";
import $ from "jquery";
import PageComponent from "../../components/pages/default-page.vue";
// import { mapState, useStore } from "vuex";
// const store = useStore();
export default {
  components: {
    PageComponent,
  },
  data() {
    return {
      channel: null,
      data: {
        inventories: [],
      },
      checkInOutModal: false,
      checkInOutModalInv: {},
      checkInOutInventory: null,
      checkInOrOutStatus: "checkin",
      checkInForm: {
        quantity: 0,
        remarks: "",
        inventory_id: "",
        // staff_id: "",
        mode: "checkin",
      },
      checkOutForm: {
        quantity: 0,
        remarks: "",
        inventory_id: -1,
        // staff_id: "",
        mode: "checkout",
      },
    };
  },

  methods: {
    openCheckInOutModal(id) {
      let inventory = _.find(this.data.inventories, { id: id });
      this.checkInOutModalInv = inventory;
      this.checkInOutModal = true;
    },
    handleTabClickingEvent(name) {
      this.checkInOrOutStatus = name;
      console.log(this.checkInOrOutStatus);
    },
    async checkInOutStock() {
      if (this.checkInOrOutStatus == "checkin") {
        console.log("checkInStock");
        this.checkInForm.inventory_id = this.checkInOutModalInv.id;
        // this.checkInForm.staff_id = this.$store.getters.getUser.id;
        // const res = await this.callApi("POST", "/api/stock", this.checkInForm);
        console.time("newStock");
        const res = await this.$axiosClient.post("/stock", this.checkInForm);
        if (res.status == 201) {
          this.checkInForm = {
            quantity: 0,
            remarks: "",
            inventory_id: "",
            // staff_id: "",
          };
          this.checkInOutModal = false;
          this.success(
            "Successfully checked in INV_ID: " + this.checkInOutModalInv.id
          );
        } else if (res.status == 422) {
          console.log(res);
          this.error(Object.values(res.data.errors)[0], res.data.message);
        } else {
          this.checkInForm = {
            quantity: 0,
            remarks: "",
            inventory_id: "",
            // staff_id: "",
          };
          this.checkInOutModal = false;
          this.smtgWentWrong();
        }
      } else if (this.checkInOrOutStatus == "checkout") {
        if (this.checkOutForm.quantity >= this.checkInOutModalInv.qty_on_hand) {
          this.error(
            "The quantity to check out cannot be larger than the quantity on hand! Please try again."
          );
        } else {
          //   this.checkOutForm.quantity = -Math.abs(this.checkOutForm.quantity);
          console.log("checkOutStock");
          this.checkOutForm.inventory_id = this.checkInOutModalInv.id;
          // this.checkOutForm.staff_id = 1;
          // const res = await this.callApi(
          //   "POST",
          //   "/api/stock",
          //   this.checkOutForm
          // );
          console.time("newStock");
          const res = await this.$axiosClient.post("/stock", this.checkOutForm);

          if (res.status == 201) {
            this.checkOutForm = {
              quantity: 0,
              remarks: "",
              inventory_id: "",
              // staff_id: "",
            };
            this.checkInOutModal = false;
            this.success(
              "Successfully checked out INV_ID: " + this.checkInOutModalInv.id
            );
          } else if (res.status == 422) {
            console.log(res);
            this.error(Object.values(res.data.errors)[0], res.data.message);
          } else {
            this.checkOutForm = {
              quantity: 0,
              remarks: "",
              inventory_id: "",
              // staff_id: "",
            };
            this.checkInOutModal = false;
            this.smtgWentWrong();
          }
        }
      }
    },

    async getUpdatedInventory(e) {
      await this.$axiosClient
        .get("/inventory/" + e.inventory.id)
        .then((response) => {
          let invToReplace = _.findIndex(this.data.inventories, {
            id: response.data.data.id,
          });
          this.data.inventories[invToReplace] = response.data.data;
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
  },

  async created() {
    await this.$axiosClient
      .get("/getInvByWarehouse/" + this.$store.getters.getUser.warehouse_id)
      .then((response) => {
        console.log(response.data.data);

        this.data.inventories = response.data.data;
        console.log("store", this.$store.getters.getUser);
      })
      .catch((error) => {
        this.handleApiError(error);
      });

    $(document).ready(function () {
      $("#inventories").DataTable();
    });
  },
  mounted() {
    let pusher = new Pusher("5838775c3e3a60a69748", {
      cluster: "ap1",
      encrypted: false,
    });
    let channel = pusher.subscribe(
      "check-in-out-stock." + this.$store.getters.getUser.warehouse_id
    );
    console.log(channel);
    let event = `App\\Events\\StockCreated`;
    console.log("event", event);

    channel.bind(event, (e) => {
      console.log("stock created", e);
      this.getUpdatedInventory(e);
      console.timeEnd("newStock");
    });
  },
};
</script>