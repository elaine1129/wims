<template>
  <table :id="name" class="display" style="width: 100%">
    <thead>
      <tr>
        <th>Stock ID</th>
        <th v-if="this.$store.getters.getUser.role != 'Admin'">Inventory ID</th>
        <th v-if="this.$store.getters.getUser.role != 'Admin'">
          Inventory Name
        </th>

        <!-- <th>Original Count</th> -->
        <th>Quantity in/out</th>
        <th>Date</th>

        <!-- <th>Total Count</th> -->
        <th>Staff Responsible</th>
        <th>Remarks</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="stock in data" :key="stock.id">
        <td>{{ stock.id }}</td>
        <td v-if="this.$store.getters.getUser.role != 'Admin'">
          {{ stock.inventory.id }}
        </td>
        <td v-if="this.$store.getters.getUser.role != 'Admin'">
          {{ stock.inventory.name }}
        </td>
        <td>{{ stock.quantity }}</td>
        <td>{{ stock.created_at }}</td>

        <td>{{ stock.staff.name }}</td>
        <td>{{ stock.remarks }}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: {
    data: Array,
    name: String,
  },

  beforeUpdate() {
    let temp = "#" + this.$props.name;
    if ($.fn.dataTable.isDataTable(temp)) {
      table = $(temp).DataTable();
      table.destroy();
    }
    $(document).ready(function () {
      $(temp).DataTable({
        // destroy: true,
        scrollY: "200px",
        scrollCollapse: true,
      });
    });
  },
};
</script>