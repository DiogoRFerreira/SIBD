#Functions and Triggers

#Functions

delimiter $$
create function min_start_maior_new_end_connects(s_num integer, new_manuf varchar(255), new timestamp)
returns timestamp
begin
  declare m_start timestamp;
  set m_start = NULL;
  select start into m_start
  from Connects as c
  where c.snum = s_num
    and c.manuf = new_manuf
    and end > new
    and end <= all(select end
                    from Connects
                    where snum = s_num
                    and manuf = new_manuf
                    and end > new);
 return m_start;
end$$
delimiter ;

delimiter $$
create function min_start_maior_new_end_wears(pat integer, new timestamp) returns timestamp
begin
  declare m_start timestamp;
  set m_start = NULL;
  select start into m_start
  from Wears as w
  where w.patient = pat
  and end > new
  and end <= all(select end
                  from Wears
                  where patient = pat
                  and end > new);
  return m_start;
end$$
delimiter ;


delimiter $$
create function max_end_menor_new_start_connects(s_num integer, new_manuf varchar(255), new timestamp)
returns timestamp
begin
  declare m_end timestamp;
  set m_end = NULL;
  select end into m_end
  from Connects as c
  where c.snum = s_num
  and c.manuf = new_manuf
  and start < new
  and start >= all(select start
                    from Connects as c2
                    where c2.snum = s_num
                    and c2.manuf = new_manuf
                    and start < new);
  return m_end;
end$$
delimiter ;

delimiter $$
create function max_end_menor_new_start_wears(pat integer, new timestamp) returns timestamp
begin
  declare m_end timestamp;
  set m_end = NULL;
  select end into m_end
  from Wears as w
  where w.patient = pat
  and start < new
  and start >= all(select start
                    from Wears
                    where patient = pat
                    and start < new);
  return m_end;
end$$
delimiter ;

#Triggers

delimiter $$
create trigger check_overlapping_periods_insert_connects before insert on Connects for each row
begin
      if(exists(select snum from Connects where end > new.start and end < new.end
        and snum = new.snum and manuf = new.manuf)) then
        call Device_already_connected_in_that_time();
      end if;
      if(exists(select snum from Connects where start > new.start and start <
        new.end and snum = new.snum and manuf = new.manuf)) then
        call Device_already_connected_in_that_time();
      end if;
      if (min_start_maior_new_end_connects(new.snum, new.manuf, new.end) IS NOT NULL) then
        if(new.end > min_start_maior_new_end_connects(new.snum, new.manuf, new.end)) then
          call Device_already_connected_in_that_time();
        end if;
      end if;
      if (max_end_menor_new_start_connects(new.snum, new.manuf, new.start) IS NOT NULL) then
        if(new.start < max_end_menor_new_start_connects(new.snum, new.manuf, new.start)) then
          call Device_already_connected_in_that_time();
        end if;
      end if;
end$$
delimiter;

delimiter $$
create trigger check_overlapping_periods_update_connects before update on Connects
for each row
begin
      if(new.end > old.end or new.start < old.start)then
        if(exists(select snum from Connects where end > new.start and end <
          new.end and snum = new.snum and manuf = new.manuf and end <>
          old.end)) then
          call Device_already_connected_in_that_time();
        end if;
        if(exists(select snum from Connects where start > new.start and start < new.end and snum = new.snum and manuf = new.manuf and start <> old.start)) then
          call Device_already_connected_in_that_time();
        end if;
        if (min_start_maior_new_end_connects(new.snum, new.manuf, new.end) IS NOT NULL) then
          if(new.end > min_start_maior_new_end_connects(new.snum, new.manuf, new.end) and min_start_maior_new_end_connects(new.snum, new.manuf, new.end) <> old.start) then
            call Device_already_connected_in_that_time();
          end if;
        end if;
        if (max_end_menor_new_start_connects(new.snum, new.manuf, new.start) IS NOT NULL) then
          if(new.start < max_end_menor_new_start_connects(new.snum, new.manuf, new.start) and max_end_menor_new_start_connects(new.snum, new.manuf, new.start) <> old.end) then
            call Device_already_connected_in_that_time();
          end if;
        end if;
      end if;
end$$
delimiter;


delimiter $$
create trigger check_overlapping_periods_insert_wears before insert on Wears for each row
begin
      if(exists(select patient from Wears where end > new.start and end < new.end and patient = new.patient)) then
        call Patient_already_connected_in_that_time();
      end if;
      if(exists(select patient from Wears where start > new.start and start < new.end and patient = new.patient)) then
        call Patient_already_connected_in_that_time();
      end if;
      if (min_start_maior_new_end_wears(new.patient, new.end) IS NOT NULL) then
        if(new.end > min_start_maior_new_end_wears(new.patient, new.end)) then
          call Patient_already_connected_in_that_time();
        end if;
      end if;
      if (max_end_menor_new_start_wears(new.patient, new.start) IS NOT NULL) then
        if(new.start < max_end_menor_new_start_(new.patient, new.start)) then
          call Patient_already_connected_in_that_time();
        end if;
      end if;
end$$
delimiter;

delimiter $$
create trigger check_overlapping_periods_update_wears before update on Wears for each row
begin
      if(new.end > old.end or new.start < old.start)then
        if(exists(select patient from Wears where end > new.start and end < new.end and patient = new.patient and end <> old.end)) then
          call Patient_already_connected_in_that_time();
        end if;
        if(exists(select patient from Wears where start > new.start and start < new.end and patient = new.patient and start <> old.start) IS NOT NULL) then
          call Patient_already_connected_in_that_time();
        end if;
        if (min_start_maior_new_end_wears(new.patient, new.end) IS NOT NULL) then
          if(new.end > min_start_maior_new_end_wears(new.patient, new.end) and min_start_maior_new_end_wears(new.patient, new.end) <> old.start) then
            call Patient_already_connected_in_that_time();
          end if;
        end if;
        if (max_end_menor_new_start_wears(new.patient, new.start) IS NOT NULL) then
          if(new.start < max_end_menor_new_start_(new.patient, new.start) and max_end_menor_new_start_(new.patient, new.start) <> old.end) then
            call Patient_already_connected_in_that_time();
          end if;
        end if;
      end if;
end$$
delimiter;
