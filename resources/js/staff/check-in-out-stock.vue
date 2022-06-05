<template>
  <div class="content container-fluid">
    <div class="_box_shadow _border_radious _mar_b30 _p20">
      Check in and out stock
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
            <td>{{ inventory.storage_bin[0].bin_number }}</td>
            <td>{{ inventory.category.name }}</td>
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
  </div>
</template>

<script>
</script>

<script>
import "jquery/dist/jquery.min.js";
//Datatable Modules
import "datatables.net-dt/js/dataTables.dataTables";
import "datatables.net-dt/css/jquery.dataTables.min.css";
import $ from "jquery";
export default {
  data() {
    return {
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
        staff_id: "",
        mode: "checkin",
      },
      checkOutForm: {
        quantity: 0,
        remarks: "",
        inventory_id: -1,
        staff_id: "",
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
        this.checkInForm.staff_id = 1;
        const res = await this.callApi("POST", "/api/stock", this.checkInForm);
        if (res.status == 201) {
          this.checkInForm = {
            quantity: 0,
            remarks: "",
            inventory_id: "",
            staff_id: "",
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
            staff_id: "",
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
          this.checkOutForm.staff_id = 1;
          const res = await this.callApi(
            "POST",
            "/api/stock",
            this.checkOutForm
          );
          if (res.status == 201) {
            this.checkOutForm = {
              quantity: 0,
              remarks: "",
              inventory_id: "",
              staff_id: "",
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
              staff_id: "",
            };
            this.checkInOutModal = false;
            this.smtgWentWrong();
          }
        }
      }
    },
  },
  async created() {
    const res = await this.callApi("GET", "/api/inventories");
    if (res.status == 200) {
      console.log(res.data.data);

      this.data.inventories = res.data.data;
    }
    $(document).ready(function () {
      $("#inventories").DataTable();
    });
  },
};
</script>