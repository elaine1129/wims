<template>
  <table :id="name" class="display" style="width: 100%">
    <thead>
      <tr>
        <th>Cycle Count ID</th>
        <th>SKU ID</th>
        <th>Inventory Name</th>
        <th>Warehouse Name</th>
        <th>Staff Name</th>
        <th>Date Counted</th>
        <th>IRA(%)</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="cycle_counting in data" :key="cycle_counting.id">
        <td>
          <a>{{ cycle_counting.id }}</a>
        </td>
        <td>{{ cycle_counting.schedule.sku.id }}</td>
        <td>{{ cycle_counting.schedule.sku.inventory.name }}</td>
        <td>{{ cycle_counting.schedule.sku.inventory.warehouse.name }}</td>
        <td>{{ cycle_counting.schedule.staff.name }}</td>
        <td>{{ convertDate(cycle_counting.created_at) }}</td>
        <td>{{ cycle_counting.inv_rec_accuracy }}</td>
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
};
</script>