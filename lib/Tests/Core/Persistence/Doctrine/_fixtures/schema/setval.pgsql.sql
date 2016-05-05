SELECT setval('ngbm_layout_id_seq', max(id)) FROM ngbm_layout;
SELECT setval('ngbm_block_id_seq', max(id)) FROM ngbm_block;
SELECT setval('ngbm_collection_id_seq', max(id)) FROM ngbm_collection;
SELECT setval('ngbm_collection_item_id_seq', max(id)) FROM ngbm_collection_item;
SELECT setval('ngbm_collection_query_id_seq', max(id)) FROM ngbm_collection_query;
