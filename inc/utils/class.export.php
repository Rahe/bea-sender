<?php

class Bea_Sender_Export {
	private static $header_titles;

	function __construct() {
		$this->header_titles = apply_filters(
			'bea_sender_csv_headers', array(
				'Id',
				'Email',
				'Current status',
				'Bounce cat',
				'Bounce type',
				'Bounce no'
			)
		);
	}

	/**
	 * Retrieve datas for globals or single campaign
	 *
	 * @param int $campaign_id
	 *
	 * @return mixed|void
	 */
	private static function generate_csv_campaign( $campaign_id = 0 ) {
		global $wpdb;

		if ( ! isset( $campaign_id ) || (int) $campaign_id <= 0 ) {
			$contacts = $wpdb->get_results(
				"SELECT
					r.id,
					email,
					r.current_status,
					bounce_cat,
					bounce_type,
					bounce_no,
					id_campaign,
					id_content,
					response,
					re.current_status as c_current_status
				FROM $wpdb->bea_s_receivers as r
					LEFT JOIN $wpdb->bea_s_re_ca AS re
					ON r.id = re.id_receiver
				"
			);
		} else {
			$contacts = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT
					r.id,
					email,
					r.current_status,
					bounce_cat,
					bounce_type,
					bounce_no,
					id_campaign,
					id_content,
					response,
					re.current_status as c_current_status
				FROM $wpdb->bea_s_receivers as r
					LEFT JOIN $wpdb->bea_s_re_ca AS re
					ON r.id = re.id_receiver
				WHERE re.id_campaign = %d", absint( $_GET['c_id'] )
				)
			);
		}

		foreach ( $contacts as $contact ) {
			$list[] = apply_filters(
				'bea_sender_csv_item', array(
					$contact->id,
					$contact->email,
					$contact->current_status,
					$contact->bounce_cat,
					$contact->bounce_type,
					$contact->bounce_no
				), $contact
			);
		}

		return apply_filters( 'bea_sender_csv_list', $list, $contacts );
	}

	public static function export_campaign( $campaign_id = 0 ) {
		return self::generate_csv_campaign( $campaign_id );
	}

	public static function get_Header_titles() {
		return self::$header_titles;
	}
}