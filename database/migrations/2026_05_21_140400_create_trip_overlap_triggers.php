<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Defense-in-depth overlap prevention triggers per CLAUDE.md §6 Phase 5.
 *
 * Four triggers: car_id × (INSERT, UPDATE), driver_id × (INSERT, UPDATE).
 *
 * Application-layer BookingAvailabilityService is the primary gatekeeper and
 * provides nice error messages with conflict details. These triggers are the
 * last line of defense against bypass (raw SQL, race conditions, model events
 * being skipped). They SIGNAL SQLSTATE 45000 with a generic message — fine,
 * because at this point we want the request to fail loudly.
 *
 * SKIPPED on SQLite (tests): triggers use MariaDB/MySQL-specific syntax.
 * The application-layer service is fully exercised by tests on sqlite.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::unprepared($this->carInsertTrigger());
        DB::unprepared($this->carUpdateTrigger());
        DB::unprepared($this->driverInsertTrigger());
        DB::unprepared($this->driverUpdateTrigger());
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trips_no_car_overlap_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trips_no_car_overlap_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trips_no_driver_overlap_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trips_no_driver_overlap_update');
    }

    private function carInsertTrigger(): string
    {
        return <<<'SQL'
CREATE TRIGGER trips_no_car_overlap_insert
BEFORE INSERT ON trips
FOR EACH ROW
BEGIN
  DECLARE conflict_count INT;
  IF NEW.status NOT IN ('cancelled', 'no_show', 'completed', 'closed') THEN
    SELECT COUNT(*) INTO conflict_count
    FROM trips
    WHERE car_id = NEW.car_id
      AND status NOT IN ('cancelled', 'no_show', 'completed', 'closed')
      AND deleted_at IS NULL
      AND id != NEW.id
      AND NOT (
        scheduled_end <= NEW.scheduled_start
        OR scheduled_start >= NEW.scheduled_end
      );
    IF conflict_count > 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Car booking overlap detected';
    END IF;
  END IF;
END
SQL;
    }

    private function carUpdateTrigger(): string
    {
        return <<<'SQL'
CREATE TRIGGER trips_no_car_overlap_update
BEFORE UPDATE ON trips
FOR EACH ROW
BEGIN
  DECLARE conflict_count INT;
  IF NEW.status NOT IN ('cancelled', 'no_show', 'completed', 'closed') THEN
    SELECT COUNT(*) INTO conflict_count
    FROM trips
    WHERE car_id = NEW.car_id
      AND status NOT IN ('cancelled', 'no_show', 'completed', 'closed')
      AND deleted_at IS NULL
      AND id != NEW.id
      AND NOT (
        scheduled_end <= NEW.scheduled_start
        OR scheduled_start >= NEW.scheduled_end
      );
    IF conflict_count > 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Car booking overlap detected';
    END IF;
  END IF;
END
SQL;
    }

    private function driverInsertTrigger(): string
    {
        return <<<'SQL'
CREATE TRIGGER trips_no_driver_overlap_insert
BEFORE INSERT ON trips
FOR EACH ROW
BEGIN
  DECLARE conflict_count INT;
  IF NEW.status NOT IN ('cancelled', 'no_show', 'completed', 'closed') THEN
    SELECT COUNT(*) INTO conflict_count
    FROM trips
    WHERE driver_id = NEW.driver_id
      AND status NOT IN ('cancelled', 'no_show', 'completed', 'closed')
      AND deleted_at IS NULL
      AND id != NEW.id
      AND NOT (
        scheduled_end <= NEW.scheduled_start
        OR scheduled_start >= NEW.scheduled_end
      );
    IF conflict_count > 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Driver booking overlap detected';
    END IF;
  END IF;
END
SQL;
    }

    private function driverUpdateTrigger(): string
    {
        return <<<'SQL'
CREATE TRIGGER trips_no_driver_overlap_update
BEFORE UPDATE ON trips
FOR EACH ROW
BEGIN
  DECLARE conflict_count INT;
  IF NEW.status NOT IN ('cancelled', 'no_show', 'completed', 'closed') THEN
    SELECT COUNT(*) INTO conflict_count
    FROM trips
    WHERE driver_id = NEW.driver_id
      AND status NOT IN ('cancelled', 'no_show', 'completed', 'closed')
      AND deleted_at IS NULL
      AND id != NEW.id
      AND NOT (
        scheduled_end <= NEW.scheduled_start
        OR scheduled_start >= NEW.scheduled_end
      );
    IF conflict_count > 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Driver booking overlap detected';
    END IF;
  END IF;
END
SQL;
    }
};
