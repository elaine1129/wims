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
            <td><Button type="primary">Check In/Out Stock</Button></td>
          </tr>
        </tbody>
      </table>
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
    };
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