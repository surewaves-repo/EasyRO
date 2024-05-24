--GetActivitySeconds --

CREATE PROCEDURE `GetActivitySeconds`(IN regionId int,IN networkRoNumber nvarchar(200),
 IN startDate DATETIME,IN endDate DATETIME ,IN customerName nvarchar(100))
BEGIN

	select sum(sva.playduration) as seconds,svc.channel_id,ra.internal_ro_number  
	from sv_advertiser_report sva join ro_approved_campaigns ra 
	ON sva.campaign_id = ra.campaign_id join ro_network_ro_report_details rrr
	ON rrr.internal_ro_number = ra.internal_ro_number 
	join sv_advertiser_campaign svc on svc.campaign_id = ra.campaign_id
	join sv_tv_channel stc ON stc.tv_channel_id = svc.channel_id
	join sv_customer sc ON sc.customer_id = stc.enterprise_id
	WHERE sva.screen_region_id = regionId  
	and rrr.network_ro_number = networkRoNumber 
	and sva.schedule_date between startDate and endDate
	and sc.customer_name = customerName
	and sva.requested_url is null
	group by svc.channel_id;

END

-- Campaign_Performance_Report  --

CREATE PROCEDURE `Campaign_Performance_Report`(IN `startDate` DATETIME, IN `endDate` DATETIME, IN `startIndex` INT, IN `endIndex` INT, IN `isCount` BIT, IN `isTestUser` TINYINT)
BEGIN

	DECLARE roCount,iLoop,rowID,chID INT DEFAULT 0 ;
	DECLARE playedSpotSec,playedBannerSec, scheduledSpotSec, scheduledBannerSec decimal(25,4) DEFAULT 0.0;
	DECLARE nRO ,custName,cName,iRO NVARCHAR(1000) DEFAULT ''; 
	/*MSO	Channel	Net Work RO	Billing Name	Start Dt	End Dt	Scheduled	Actual 
	Spot Rate	Banner Rate TotalPayable  */
	CREATE TEMPORARY TABLE tmpPerformanceStore (id INT NOT NULL AUTO_INCREMENT,mso NVARCHAR(600),channelName NVARCHAR(600), networkRoNumber NVARCHAR(1000),
	billingName NVARCHAR(600),startDT datetime,endDT datetime,scheduledSpotSeconds decimal(25,4)	,
	playedSpotSeconds decimal(25,4)	,scheduledBannerSeconds decimal(25,4)	,playedBannerSeconds decimal(25,4),
	spotRate float,bannerRate float,totalPayable decimal(25,4),internalRoNumber NVARCHAR(1000),channelId INT,PRIMARY KEY (id));

	INSERT INTO tmpPerformanceStore (mso ,channelName , networkRoNumber ,
	billingName ,startDT ,endDT ,scheduledSpotSeconds ,	playedSpotSeconds ,scheduledBannerSeconds,
	playedBannerSeconds ,spotRate ,bannerRate,totalPayable,internalRoNumber,channelId )
    (select ran.customer_name,ran.channel_name,rnor.network_ro_number,ran.billing_name,
	ero.camp_start_date,ero.camp_end_date,0,0,0,0,ran.channel_spot_avg_rate,
	ran.channel_banner_avg_rate,0,ero.internal_ro,ran.tv_channel_id
	from ro_am_external_ro ero 
	inner join ro_approved_networks ran ON ero.internal_ro = ran.internal_ro_number
	inner join ro_network_ro_report_details rnor ON rnor.internal_ro_number = ero.internal_ro
	where ero.camp_end_date between startDate and endDate
	and rnor.customer_name = ran.customer_name and ero.test_user_creation=isTestUser);

	SELECT COUNT(*) INTO roCount from tmpPerformanceStore;

	IF ( isCount = 1)
	THEN
		SELECT roCount;
		
	ELSE
		/*	Select * from tmpPerformanceStore limit startIndex,endIndex;
		END IF;*/

		SET iLoop = startIndex;
		WHILE iLoop < endIndex DO
		
		SELECT id INTO rowID FROM tmpPerformanceStore where id = iLoop;
		SELECT channelName INTO cName FROM tmpPerformanceStore where id = iLoop;
		SELECT mso INTO custName FROM tmpPerformanceStore where id = iLoop;
		SELECT networkRoNumber INTO nRO  FROM tmpPerformanceStore where id = iLoop;
		SELECT channelId INTO chID FROM tmpPerformanceStore where id = iLoop;
		SELECT internalRoNumber INTO iRO  FROM tmpPerformanceStore where id = iLoop;

		select sum(impressions)*sva.ro_duration INTO scheduledSpotSec
		from  sv_advertiser_campaign sva inner join  sv_advertiser_campaign_screens_dates acs
		ON acs.campaign_id = sva.campaign_id and sva.channel_id = chID and sva.selected_region_id = 1
		where acs.status != 'cancelled' and sva.internal_ro_number = iRO;

		select sum(impressions)*sva.ro_duration INTO scheduledBannerSec
		from  sv_advertiser_campaign sva inner join  sv_advertiser_campaign_screens_dates acs
		ON acs.campaign_id = sva.campaign_id and sva.channel_id = chID and sva.selected_region_id = 3
		where acs.status != 'cancelled' and sva.internal_ro_number = iRO;

		IF ( scheduledSpotSec > 0 AND scheduledSpotSec IS NOT NULL)
		THEN
			select sum(sva.playduration)  INTO playedSpotSec
			from sv_advertiser_report sva join sv_advertiser_campaign svc
			ON sva.campaign_id = svc.campaign_id 
			where svc.channel_id = chID
			and svc.internal_ro_number = iRO
			and sva.screen_region_id = 1 and sva.requested_url is null ;
		END IF;
		IF ( scheduledBannerSec > 0 AND scheduledBannerSec IS NOT NULL)
		THEN
			select sum(sva.playduration)  INTO playedBannerSec
			from sv_advertiser_report sva join sv_advertiser_campaign svc
			ON sva.campaign_id = svc.campaign_id 
			where svc.channel_id = chID
			and svc.internal_ro_number = iRO
			and sva.screen_region_id = 3 and sva.requested_url is null ;
		END IF;

		UPDATE tmpPerformanceStore SET scheduledSpotSeconds = scheduledSpotSec where id = iLoop;
		UPDATE tmpPerformanceStore SET scheduledBannerSeconds = scheduledBannerSec where id = iLoop;

		IF ( playedSpotSec > scheduledSpotSec)
		THEN
			SET playedSpotSec = scheduledSpotSec;
		END IF;
		IF ( playedBannerSec > scheduledBannerSec)
		THEN
			SET playedBannerSec = scheduledBannerSec;
		END IF;

		UPDATE tmpPerformanceStore SET playedSpotSeconds = playedSpotSec where id = iLoop;
		UPDATE tmpPerformanceStore SET playedBannerSeconds = playedBannerSec where id = iLoop;

		SET iLoop = iLoop + 1;
		END WHILE;
		
		SELECT * FROM  tmpPerformanceStore limit startIndex,endIndex;
	END IF;

	DROP TABLE tmpPerformanceStore;

END		