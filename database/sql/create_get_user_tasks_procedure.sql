CREATE PROCEDURE GetUserTasks(
    IN user_id INT,
    IN status_id ENUM("pending","inprogress","completed")
)
BEGIN
    SELECT * FROM task_statuses ts
    JOIN tasks t
        ON t.status_id = ts.id
    WHERE t.user_id = user_id AND t.status_id = status_id;
END;