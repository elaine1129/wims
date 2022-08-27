<template>
  <table :id="name" class="display" style="width: 100%">
    <thead>
      <tr>
        <th>SKU ID</th>
        <th>Cycle Counting ID</th>
        <th>Inventory ID</th>
        <th>Inventory Name</th>
        <th>Storage Number</th>
        <th>Schedule</th>
        <th>Date counted</th>
        <th>System count</th>
        <th>Actual count</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="cycle_count in data" :key="cycle_count.id">
        <td>{{ cycle_count.schedule.sku.id }}</td>
        <td>{{ cycle_count.id }}</td>
        <td>{{ cycle_count.schedule.sku.inventory.id }}</td>
        <td>{{ cycle_count.schedule.sku.inventory.name }}</td>
        <td>
          {{
            cycle_count.schedule.storage_bin.length > 0
              ? cycle_count.schedule.storage_bin[0].bin_number
              : "-"
          }}
        </td>
        <td>
          {{
            cycle_count.schedule.schedule ? cycle_count.schedule.schedule : "-"
          }}
        </td>
        <td>{{ cycle_count.created_at }}</td>
        <td>{{ cycle_count.recorded_count }}</td>
        <td>{{ cycle_count.actual_count }}</td>
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