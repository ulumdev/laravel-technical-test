<template>
    <div>
        <h2>Detail Project</h2>
        <p><strong>Name:</strong> {{ project.name }}</p>
        <p><strong>Details:</strong> {{ project.details }}</p>
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
        <Link href="/projects">Back</Link>
    </div>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
defineProps({ project: Object, audits: Array });
</script>
