
# WordPress Task Manager API

## Overview

This WordPress plugin provides a RESTful API for managing tasks. It allows you to perform CRUD (Create, Read, Update, Delete) operations on tasks, providing flexibility for integration with external applications.
Plugin serves as a RESTful API for a specialized to-do list application. The plugin  showcases my proficiency in coding standards, creativity in problem-solving, and ability in integrating functionalities within the WordPress framework.
This plugin shows admin a list of notices based on the tasks that are due today/current_date and are open/in_progress.

## API Endpoints

### 1. Add a New Task

- **Endpoint:** `/wptaskmanager/v1/tasks/add_task`
- **Method:** `POST`
- **Parameters:**
  - `title` (required): Task title.
  - `description`: Task description (optional).
  - `due_date`: Due date of the task (optional).
  - `status`: Status of the task (optional).

### 2. Update a Task

- **Endpoint:** `/wptaskmanager/v1/tasks/update_task`
- **Method:** `PUT`
- **Parameters:**
  - `id` (required): ID of the task to be updated.
  - `title`: Updated task title.
  - `description`: Updated task description.
  - `status`: Updated task status.
  - `due_date`: Updated due date.

### 3. Delete a Task

- **Endpoint:** `/wptaskmanager/v1/tasks/delete_task`
- **Method:** `DELETE`
- **Parameters:**
  - `id` (required): ID of the task to be deleted.

### 4. Get All Tasks

- **Endpoint:** `/wptaskmanager/v1/tasks/all_tasks`
- **Method:** `GET`
- **Parameters:**
  - `search`: Search keyword.(post_title, post_description)
  - `due_start_date`: Tasks due on or after this date.
  - `due_end_date`: Tasks due on or before this date.
  - `status`: Array of task statuses.

## Authentication

Authentication is handled through JWT tokens. Include the valid JWT token in the `_token` parameter of your requests.

## Permissions

To interact with the API, the user must have a valid JWT token. Ensure that your application includes this token in the requests.

## Examples

### Add a New Task

POST /wptaskmanager/v1/tasks/add_task
Content-Type: application/json

{
  "title": "Sample Task",
  "description": "This is a sample task.",
  "due_date": "2023-12-31",
  "status": "In Progress"
}
#### Response
{
    "code": "success",
    "data": {
        "status": 200,
        "task_id": 9,
        "message": "Task created successfully."
    }
}
#### Update a  Task

PUT /wptaskmanager/v1/tasks/update_task
Content-Type: application/json

{
  "id": 123,
  "title": "Updated Task",
  "description": "This task has been updated.",
  "status": "Completed",
  "due_date": "2023-12-25"
}
#### Response
{
    "code": "success",
    "data": {
        "status": 200,
        "task_id": 6,
        "message": "Task updated successfully."
    }
}

### Delete a Task

PUT /wptaskmanager/v1/tasks/delete_task
Content-Type: application/json

{
  "id": 123,
}
### Response
{
    "code": "success",
    "data": {
        "status": 200,
        "task_id": 9,
        "message": "Task deleted successfully."
    }
}

### Get all tasks
You can get all tasks based on specific filters. Like between specific dates( due_start_date <-> due_end_date), based on 
specific status (completed,open or in_progress) or based on post title and description.If non parameter added it will return all tasks.
PUT /wptaskmanager/v1/tasks/all_tasks
Content-Type: application/json

{
  [{"key":"search","value":"Awesome","equals":true,"description":"","enabled":true},{"key":"status[]","value":"completed","equals":true,"description":"","enabled":true},{"key":"due_start_date","value":"2023-11-12","equals":true,"description":"","enabled":true},{"key":"due_end_date","value":"2023-11-14","equals":true,"description":"","enabled":true},{"key":"status[]","value":"in_progress","equals":true,"description":"","enabled":true}]
}

### Response
{
    "code": "success",
    "status": 200,
    "data": [
        {
            "ID": 7,
            "post_author": "1",
            "post_date": "2023-11-12 19:50:48",
            "post_date_gmt": "2023-11-12 19:50:48",
            "post_content": "just looking like a vow",
            "post_title": "Awesome",
            "post_excerpt": "",
            "post_status": "publish",
            "comment_status": "closed",
            "ping_status": "closed",
            "post_password": "",
            "post_name": "awesome-2",
            "to_ping": "",
            "pinged": "",
            "post_modified": "2023-11-12 20:01:48",
            "post_modified_gmt": "2023-11-12 20:01:48",
            "post_content_filtered": "",
            "post_parent": 0,
            "guid": "http://wp-tasks.local/wptaskmanager/awesome-2/",
            "menu_order": 0,
            "post_type": "wptaskmanager",
            "post_mime_type": "",
            "comment_count": "0",
            "filter": "raw"
        },
        {
            "ID": 6,
            "post_author": "1",
            "post_date": "2023-11-12 19:50:45",
            "post_date_gmt": "2023-11-12 19:50:45",
            "post_content": "just looking like a vow",
            "post_title": "Awesome",
            "post_excerpt": "",
            "post_status": "publish",
            "comment_status": "closed",
            "ping_status": "closed",
            "post_password": "",
            "post_name": "awesome",
            "to_ping": "",
            "pinged": "",
            "post_modified": "2023-11-12 20:00:59",
            "post_modified_gmt": "2023-11-12 20:00:59",
            "post_content_filtered": "",
            "post_parent": 0,
            "guid": "http://wp-tasks.local/wptaskmanager/awesome/",
            "menu_order": 0,
            "post_type": "wptaskmanager",
            "post_mime_type": "",
            "comment_count": "0",
            "filter": "raw"
        }
    ],
    "message": "Tasks list."
}

