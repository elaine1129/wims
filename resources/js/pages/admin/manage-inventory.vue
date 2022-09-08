<template>
  <PageComponent title="Manage Inventory">
    <div class="flex justify-between">
      <div>
        <Button
          type="primary"
          icon="ios-add-circle-outline"
          style="margin-bottom: 20px"
          @click="showAddInventoryModal"
          >Add</Button
        >
      </div>
      <div id="filter-warehouse"></div>
      <div></div>
    </div>

    <ViewInventoryTableComponent
      name="inventories"
      :data="data.inventories"
      @edited="editedInChild"
      @deleted="deletedInChild"
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
        <FormItem label="Priority">
          <Select v-model="addInventoryForm.priority">
            <Option v-for="index in 5" :key="index" :value="index">{{
              index
            }}</Option>
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
import ViewInventoryTableComponent from "../../components/tables/view-inventory-table.vue";
import router from "../../router";
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
        priority: 1,
      },
    };
  },
  watch: {
    "data.inventories": {
      handler() {
        var table = $("#inventories").DataTable({
          drawCallback: function (settings) {
            var api = new $.fn.dataTable.Api(settings);
            if (api.page() >= 0) {
              router.push({ query: { page: api.page() + 1 } });
            }
          },
          initComplete: function () {
            this.api()
              .columns([2])
              .every(function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                  .appendTo("#filter-warehouse")
                  .on("change", function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                    column
                      .search(val ? "^" + val + "$" : "", true, false)
                      .draw();
                  });

                column
                  .data()
                  .unique()
                  .sort()
                  .each(function (d, j) {
                    select.append(
                      '<option value="' + d + '">' + d + "</option>"
                    );
                  });
              });
          },
        });
        table.page(this.$route.query.page - 1).draw(false);
      },
      deep: true,
      flush: "post",
    },
  },
  beforeCreate() {
    if (!this.$route.query.page > 0) {
      router.push({ query: { page: 1 } });
    }
  },
  async created() {
    const res = await this.$axiosClient
      .get("/inventories")
      .then((res) => {
        this.data.inventories = res.data.data;
      })
      .catch((error) => {
        this.handleApiError(error);
      });
    console.log(res);

    // $(document).ready(function () {
    //   $("#inventories").DataTable({
    //     initComplete: function () {
    //       this.api()
    //         .columns([2])
    //         .every(function () {
    //           var column = this;
    //           var select = $('<select><option value=""></option></select>')
    //             .appendTo("#filter-warehouse")
    //             .on("change", function () {
    //               var val = $.fn.dataTable.util.escapeRegex($(this).val());

    //               column.search(val ? "^" + val + "$" : "", true, false).draw();
    //             });

    //           column
    //             .data()
    //             .unique()
    //             .sort()
    //             .each(function (d, j) {
    //               select.append('<option value="' + d + '">' + d + "</option>");
    //             });
    //         });
    //     },
    //   });
    // });
  },
  methods: {
    async refetchData() {
      const res = await this.$axiosClient
        .get("/inventories")
        .then((res) => {
          this.data.inventories = res.data.data;

          $("#inventories").DataTable().destroy();
          $("#filter-warehouse").empty();
        })
        .catch((error) => {
          console.log(error);
          this.handleApiError(error);
        });
      console.log(res);
    },
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
            priority: 1,
          };
          this.success(
            `Inventory Created! Bin number: ${
              response.data.bin_number
                ? response.data.bin_number
                : "nil. Assign a category to put into bin"
            }`
          );
          this.addInventoryModal = false;
          this.refetchData();
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
        priority: 1,
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
    editedInChild() {
      this.refetchData();
    },
    deletedInChild() {
      this.refetchData();
    },
  },
};
</script>