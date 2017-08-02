import jenkins.model.*
import hudson.model.*
import hudson.security.AuthorizationMatrixProperty

def instance = Jenkins.getInstance()
def job = instance.getItem('__JOB_NAME__')
def strategy = job.getProperty(AuthorizationMatrixProperty.class)

if (!strategy) {
    strategy = new AuthorizationMatrixProperty()
    job.addProperty(strategy)
}

strategy.add(hudson.model.Item.BUILD, "L_APPL_JNKNS_DV")
strategy.add(hudson.model.Item.CANCEL, "L_APPL_JNKNS_DV")
strategy.add(hudson.model.Item.DISCOVER, "L_APPL_JNKNS_DV")
strategy.add(hudson.model.Item.READ, "L_APPL_JNKNS_DV")
strategy.add(hudson.model.Item.WORKSPACE, "L_APPL_JNKNS_DV")

job.save()