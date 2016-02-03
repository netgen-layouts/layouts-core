SELECT setval('ngbm_rule_id_seq', max(id)) FROM ngbm_rule;
SELECT setval('ngbm_rule_value_id_seq', max(id)) FROM ngbm_rule_value;
SELECT setval('ngbm_rule_condition_id_seq', max(id)) FROM ngbm_rule_condition;
