select l.id, p.id, l.created_ts from patient p, push_notification l  where l.patient_id = p.id  and l.patient_id = '12' and date(l.created_ts) > DATE_ADD(now() , INTERVAL -1 DAY)   order by l.id desc
