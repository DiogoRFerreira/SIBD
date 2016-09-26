#Queries

select w.patient, r.datetime, r.value, s.units
from Reading as r, Connects as c, Wears as w, Device as d, Sensor as s
where s.snum = r.snum and s.manuf = r.manuf and r.snum = c.snum
and r.manuf = c.manuf and c.pan = w.pan
and d.serialnum = c.snum and d.manufacturer = c.manuf
and c.end <= w.end and c.start >= w.start and r.datetime>=c.start and r.datetime <= c.end
and timestampdiff(month, r.datetime, current_timestamp) <= 6 and description like '%blood pressure%'
and w.patient = 1234;

select m.nut4code, m.name
from Municipality as m, Lives as l, Wears as w, Connects as c
where m.nut4code = l.muni and l.patient = w.patient and w.pan = c.pan
  and manuf = 'Philips'
  and c.end >= current_timestamp and w.end >= current_timestamp and l.end >= current_timestamp
group by nut4code
having count(distinct snum) >= all(select count(distinct snum)
                                    from Municipality as m, Lives as l, Wears as w, Connects as c
                                    where m.nut4code = l.muni and l.patient = w.patient
                                    and w.pan = c.pan
                                    and manuf = 'Philips'
                                    and c.end >= current_timestamp and w.end >= current_timestamp and l.end >= current_timestamp
                                    group by nut4code);

select distinct d.manufacturer
from Device as d, Connects as c, Wears w, Lives as l
where d.description like '%scale%' and d.serialnum = c.snum
  and d.manufcturer = c.manuf and c.pan = w.pan
  and l.patient = w.patient and l.end >= w.end and w.end >= c.end and l.start <= w.start and w.start <= c.start
  and timestampdiff(year, c.end, current_timestamp) <= 1
  and not exists(
      select nut4code
      from Municipality
      where nut4code not in(
              select l2.muni
              from Device as d2, Connects as c2, Wears w2, Lives as l2
              where d2.description like '%scale%' and d2.serialnum = c2.snum
              and d2.manufcturer = c2.manuf
              and c2.pan = w2.pan and l2.patient = w2.patient
              and l2.end >= w2.end and w2.end >= c2.end
              and l2.start <= w2.start and w2.start <= c2.start
              and timestampdiff(year, c2.end, current_timestamp) <= 1 and d2.manufacturer = d.manufacturer));
