CREATE TABLE llx_fraispro_receipt_det
(
   rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
   fk_expensereport integer NOT NULL,
   docnumber varchar(128),										-- To store a ref of a accounting doc (piece)
   fk_c_type_fees integer NOT NULL,								-- Type of expense
   fk_c_exp_tax_cat integer,
   fk_projet integer,											-- Id of project
   comments text NOT NULL,
   product_type integer DEFAULT -1,
   qty real NOT NULL,
   subprice						double(24,8) DEFAULT 0 NOT NULL, -- unit price HT (example 100)
   subprice_ttc         		double(24,8) DEFAULT 0,    	     -- unit price if price was entered including tax
   value_unit                   double(24,8) NOT NULL,          -- P.U. TTC (example 120)
   remise_percent real,
   vat_src_code					varchar(10)  DEFAULT '',		-- Vat code used as source of vat fields. Not strict foreign key here.
   tva_tx						double(7,4),					-- Vat rate
   localtax1_tx               	double(7,4)  DEFAULT 0,    		-- localtax1 rate
   localtax1_type			 	varchar(10)	  	 NULL, 			-- localtax1 type
   localtax2_tx               	double(7,4)  DEFAULT 0,    		-- localtax2 rate
   localtax2_type			 	varchar(10)	  	 NULL, 			-- localtax2 type
   total_ht double(24,8) DEFAULT 0 NOT NULL,
   total_tva double(24,8) DEFAULT 0 NOT NULL,
   total_localtax1				double(24,8)  	DEFAULT 0,		-- Total LocalTax1 for total quantity of line
   total_localtax2				double(24,8)	DEFAULT 0,		-- total LocalTax2 for total quantity of line
   total_ttc double(24,8) DEFAULT 0 NOT NULL,
   date date NOT NULL,
   info_bits					integer DEFAULT 0,				-- TVA NPR ou non
   special_code					integer DEFAULT 0,			    -- code for special lines
   fk_multicurrency             integer,
   multicurrency_code           varchar(3),
   multicurrency_subprice       double(24,8) DEFAULT 0,
   multicurrency_subprice_ttc   double(24,8) DEFAULT 0,
   multicurrency_total_ht       double(24,8) DEFAULT 0,
   multicurrency_total_tva      double(24,8) DEFAULT 0,
   multicurrency_total_ttc      double(24,8) DEFAULT 0,
   fk_facture					integer DEFAULT 0,				-- ID of customer invoice line if expense is rebilled to a customer
   fk_ecm_files        integer DEFAULT NULL,			-- ID of ECM file that is source document of expense report
   fk_code_ventilation			integer DEFAULT 0,
   rang							integer DEFAULT 0,				-- position of line
   import_key					varchar(14),
   rule_warning_message text
) ENGINE=innodb;
