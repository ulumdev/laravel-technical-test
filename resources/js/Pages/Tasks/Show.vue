<template>
    <div>
        <h2>Detail Task</h2>
        <p><strong>Title:</strong> {{ task.title }}</p>
        <p><strong>Metadata:</strong> {{ task.metadata }}</p>
        <!-- dst... -->

        <h3>Audit Trail</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                    <th>User</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="a in audits" :key="a.id">
                    <td>{{ a.created_at }}</td>
                    <td>{{ a.event }}</td>
                    <td>{{ a.user ? a.user.name : "System" }}</td>
                    <td>{{ a.custom_note }}</td>
                </tr>
            </tbody>
        </table>
        <Link href="/tasks">Back</Link>
    </div>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
defineProps({ task: Object, audits: Array });
</script>
