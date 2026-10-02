<script setup>
const config = useRuntimeConfig()

const { data: projects, error, pending } = await useFetch(
    `${config.public.apiBase}/projects`
)
</script>

<template>
    <div class="container">
        <h1>Client Project Tracker</h1>

        <p v-if="pending">Loading projects...</p>

        <p v-else-if="error">
            Failed to load projects.
        </p>

        <div v-else>
            <p v-if="!projects?.length">
                No projects found.
            </p>

            <table v-else>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Start Date</th>
                        <th>Due Date</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="project in projects" :key="project.id">
                        <td>{{ project.id }}</td>
                        <td>{{ project.client_name }}</td>
                        <td>{{ project.project_name }}</td>
                        <td>{{ project.status }}</td>
                        <td>{{ project.priority }}</td>
                        <td>{{ project.start_date }}</td>
                        <td>{{ project.due_date }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f5f5f5;
}

.container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

th,
td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background: #f0f0f0;
}
</style>